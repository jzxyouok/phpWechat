<?php
    $qrcode=new qrCode();
    $qrcode->getqrCode();
    class qrCode{
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
        public function getqrCode()
        {
            $url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$this->getAccessToken();
            $qrcode='{"expire_seconds": 7200, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 0}}}';
            $ticket=json_decode($this->https_request($url,$qrcode),true)['ticket'];
            $url='https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket);
            $content = file_get_contents($url);
            $filename = 'QR_SCENE.jpg';
            file_put_contents($filename, $content);
            header("content-type: image/jpeg");
            $image=imagecreatefromjpeg($url);
            imagejpeg($image);
            imagedestroy($image);
        }
    }