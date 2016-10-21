<?php
    $kefu=new Kefu();
    $kefu->PostMessage('oPmBIxFEiF0CKqIegBM_S_ktSjiw','image',array("media_id"=>'-7Rmsy4VXyzOC4uqDMN4m-YHflysTGhZViLkXMFvN71o-ssCRhQFtuFSrOHwx6Y9'));
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
            require_once($_SERVER['DOCUMENT_ROOT'].'/getAccessToken.php');
            $accessToken=new AccessToken();
            return $accessToken->getAccessToken();
        }

        public function PostMessage($opendId='oPmBIxI2hEASt5vt3-CR7xbSsOn8',$msgtype='text',$content=array("content"=>""))
        {
            $accessToken=$this->getAccessToken();
            $url='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$accessToken;
            $data=json_encode(array('touser'=>$opendId,'msgtype'=>$msgtype,$msgtype=>$content),JSON_UNESCAPED_UNICODE);
            $result=$this->https_request($url,$data);
            echo $result;
        }
    }