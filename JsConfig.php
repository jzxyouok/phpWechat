<?php
    header('Content-type:text/json;charset=utf-8');
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
            require_once('./getAccessToken.php');
            $accessToken=new AccessToken();
            return $accessToken->getAccessToken();
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
        private function createNonceStr($length = 16) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $str = "";
            for ($i = 0; $i < $length; $i++) {
                $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            }
            return $str;
        }
        public function getJsConfig()
        {
            $jsapi_ticket=$this->getJsTicket();
            $noncestr = $this->createNonceStr();
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
