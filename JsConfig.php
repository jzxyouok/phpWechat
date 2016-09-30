<?php
    //header('Content-type:text/json;charset=utf-8');
    if ($_GET){
        $jsurl=$_GET['jsurl'];
        $jsConfig=new JsConfig();
        $jsConfig->jsurl=$jsurl;
        $jsConfig->getJsConfig();
    }
    class JsConfig
    {
        private $appId = 'wx54abfd3dac845fab';
        private $appsecret = 'addd017afa6d5dd22f48f69a638b9b0c';
        public  $jsurl='';
        public function https_request($url, $data = null)
        {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            if (!empty($data)) {
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($curl);
            curl_close($curl);
            return $output;
        }
        private function getAccessToken() {
            // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
            $data=null;
            if (file_exists('access_token.json')){
                $data = json_decode(file_get_contents("access_token.json"));
            }
            if ($data){
                if ($data->expire_time < time()) {
                    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appsecret";
                    $res = json_decode($this->https_request($url));
                    $access_token = $res->access_token;
                    if ($access_token) {
                        $data->expire_time = time() + 7000;
                        $data->access_token = $access_token;
                        $fp = fopen("access_token.json", "w");
                        fwrite($fp, json_encode($data));
                        fclose($fp);
                    }
                } else {
                    $access_token = $data->access_token;
                }
            }else{
                $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appsecret";
                $res = json_decode($this->https_request($url));
                $access_token = $res->access_token;
                $data = new stdClass();
                if ($access_token) {
                    $data->expire_time = time() + 7000;
                    $data->access_token = $access_token;
                    file_put_contents("access_token.json",json_encode($data));
                }
            }
            return $access_token;
        }
        private function getJsTicket() {
            $data=null;
            if (file_exists('jsapi_ticket.json')){
                $data = json_decode(file_get_contents("jsapi_ticket.json"));
            }
            if ($data){
                if ($data&&$data->expire_time < time()) {
                    $access_token = $this->getAccessToken();
                    $url='https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
                    $res=json_decode($this->https_request($url));
                    $ticket = $res->ticket;
                    if ($ticket) {
                        $data->expire_time = time() + 7000;
                        $data->jsapi_ticket = $ticket;
                        $fp = fopen("jsapi_ticket.json", "w");
                        fwrite($fp, json_encode($data));
                        fclose($fp);
                    }
                } else {
                    $ticket = $data->jsapi_ticket;
                }
            }else{
                $access_token = $this->getAccessToken();
                $url='https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
                $res=json_decode($this->https_request($url));
                $ticket = $res->ticket;
                $data=new stdClass();
                if ($ticket) {
                    $data->expire_time = time() + 7000;
                    $data->jsapi_ticket = $ticket;
                    file_put_contents("jsapi_ticket.json",json_encode($data));
                }
            }
            return $ticket;
        }
        public function getJsConfig()
        {
            $jsapi_ticket=$this->getJsTicket();
            $noncestr='Wm3WZYTPz0wzccnW';
            $timestamp=time();
            $jsurl=$this->jsurl;
            $signature="jsapi_ticket=$jsapi_ticket&noncestr=$noncestr&timestamp=$timestamp&url=$jsurl";
            $signature=sha1($signature);
            echo json_encode(
                array(
                    'data'=>array(
                        'appId'=>$this->appId,'timestamp'=>$timestamp,'nonceStr'=>$noncestr,'signature'=>$signature,'jsurl'=>$jsurl
                    ),
                    'state'=>'scussed'
                )
            );
        }
    }
