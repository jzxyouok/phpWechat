<?php
    header('Content-type:text/json;charset=utf-8');
    if ($_GET){
        $mediaIds=null;
        if (isset($_GET['mediaIds'])&&!empty($_GET['mediaIds'])){
            $mediaIds=$_GET['mediaIds'];
        }
        $Img=new downImg();
        $Img->mediaId=$mediaIds;
        echo $Img->down();
        $Img->getDown();
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

        public function getDown()
        {
            $accessToken=$this->getAccessToken();
            $mediaIds=$this->mediaId;
            foreach ($mediaIds as $key=>$value){
                $url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$accessToken.'&media_id='.$value;
                file_get_contents($url);
                $responseInfo = $http_response_header;
                $imgType=explode('/',$responseInfo[3])[1];
                $ImgCreateFun="imagecreatefrom{$imgType}";
                $ImgFun="image{$imgType}";
                $content = @$ImgCreateFun($url);
                if (!$content){
                    $content = @imagecreatefrompng($url);
                    $filename = 'images/' . $this->createName() . '.jpg';
                    @imagepng($content, $filename);
                    imagedestroy($content);
                }else{
                    $content = @$ImgCreateFun($url);
                    $filename = 'images/' . $this->createName() . '.jpg';
                    @$ImgFun($content, $filename);
                    imagedestroy($content);
                }
            }
        }
        public function down()
        {
            $result=json_encode(array('state'=>'success','mediaId'=>$this->mediaId));
            return $result;
        }
    }