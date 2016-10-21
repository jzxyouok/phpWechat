<?php
    $kefu=new Kefu();
    $kefu->PostMessage();
    class Kefu{
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
            require_once($_SERVER["DOCUMENT_ROOT"].'/getAccessToken.php');
            $accessToken=new AccessToken();
            return $accessToken->getAccessToken();
        }
        public function PostMessage($openId='oPmBIxI2hEASt5vt3-CR7xbSsOn8',$msgtype='text',$content=array("content"=>"hello world"))
        {
            $accessToken = $this->getAccessToken();
            $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $accessToken;
            $data = json_encode(array(
                "touser" => $openId,
                "msgtype" => $msgtype,
                $msgtype => $content
            ), JSON_UNESCAPED_UNICODE);
            $result = $this->https_request($url, $data);
            $ip=$this->https_request('https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$accessToken);
            echo $ip;
            echo $result;
        }
    }
