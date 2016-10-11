<?php
    class AccessToken {
        private $appId = 'wx54abfd3dac845fab';
        private $appsecret = 'addd017afa6d5dd22f48f69a638b9b0c';
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
        public function getAccessToken()
        {
            // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
            $data = null;
            if (file_exists($_SERVER["DOCUMENT_ROOT"].'/access_token.json')) {
                $data = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/access_token.json"));
            }
            if ($data) {
                if ($data->expire_time < time()) {
                    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appsecret";
                    $res = json_decode($this->https_request($url));
                    $access_token = $res->access_token;
                    if ($access_token) {
                        $data->expire_time = time() + 7000;
                        $data->access_token = $access_token;
                        $fp = fopen($_SERVER["DOCUMENT_ROOT"]."/access_token.json", "w");
                        fwrite($fp, json_encode($data));
                        fclose($fp);
                    }
                } else {
                    $access_token = $data->access_token;
                }
            }
            else {
                $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appsecret";
                $res = json_decode($this->https_request($url));
                $access_token = $res->access_token;
                $data = new stdClass();
                if ($access_token) {
                    $data->expire_time = time() + 7000;
                    $data->access_token = $access_token;
                    file_put_contents($_SERVER["DOCUMENT_ROOT"]."/access_token.json", json_encode($data));
                }
            }
            return $access_token;
        }
    }