<?php
    header('Content-type:text/json;charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $mediaIds=null;
        if (isset($_POST['mediaIds'])&&!empty($_POST['mediaIds'])){
            $mediaIds=$_POST['mediaIds'];
        }
        $Img=new downImg();
        $Img->mediaId=$mediaIds;
        echo $Img->down();
        $Img->getImg();
    }
    class  downImg{
        public $mediaId=null;
        private function getAccessToken(){
            require_once('../getAccessToken.php');
            $accessToken=new AccessToken();
            return $accessToken->getAccessToken();
        }
        private function createName($length = 5) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $str = "";
            for ($i = 0; $i < $length; $i++) {
                $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            }
            return $str;
        }
        public function getImg(){
            $accessToken=$this->getAccessToken();
            $mediaIds=$this->mediaId;
            $ch = array();
            $mh = curl_multi_init();
            $result = array();
            foreach ($mediaIds as $k=>$value) {
                $filename = 'images/' . $this->createName() . '.jpg';
                $url='http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$accessToken.'&media_id='.$value;
                $ch[$k] = curl_init($url);
                curl_setopt($ch[$k], CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch[$k], CURLOPT_CUSTOMREQUEST, 'GET');
                $fp = fopen($filename, 'wb');
                curl_setopt($ch[$k], CURLOPT_URL, $url);
                curl_setopt($ch[$k], CURLOPT_FILE, $fp);
                curl_multi_add_handle($mh, $ch[$k]);
            }
            $active = null;
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($active > 0);
            foreach ($ch as $ck => $cv) {
                $result[$ck] = curl_multi_getcontent($cv);
                usleep(1);
                curl_multi_remove_handle($mh, $cv);
            }
            curl_multi_close($mh);
        }
        public function curlDown()
        {
            $accessToken=$this->getAccessToken();
            $mediaIds=$this->mediaId;
            foreach ($mediaIds as $value) {
                $targetName = 'images/'.$this->createName().'.jpg';
                $url='http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$accessToken.'&media_id='.$value;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                $fp = fopen($targetName,'wb');
                curl_setopt($ch,CURLOPT_URL,$url);
                curl_setopt($ch,CURLOPT_FILE,$fp);
                curl_exec($ch);
                curl_close($ch);
                fclose($fp);
            }
        }
        public function getMediaIds()
        {
            $result=json_encode(array('state'=>'success','mediaId'=>$this->mediaId));
            return $result;
        }
    }