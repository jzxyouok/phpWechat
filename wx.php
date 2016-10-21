<?php
//define your token
define("TOKEN", "weixin");
$weixin=new WeixinTest();
if (isset($_GET['echostr'])){
    $weixin->valid();
}else {
    $weixin->responseMsg();
}
class WeixinTest{
    public function valid()
    {
        $signature=$_GET['signature'];
        $timestamp=$_GET['timestamp'];
        $nonce=$_GET['nonce'];
        $echorstr=$_GET['echostr'];
        $token=TOKEN;
        $array=array($token,$timestamp,$nonce);
        sort($array,SORT_STRING);
        $tmpStr=implode($array);
        $tmpStr=sha1($tmpStr);
        if ($tmpStr==$signature){
            echo $echorstr;
            exit;
        }
    }
    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!empty($postStr)){
            $msgType=trim($postObj->MsgType);
            switch ($msgType){
                case 'event':$result=$this->reviveEvent($postObj);break;//回复事件
                case 'text':$result=$this->reciveText($postObj);break;//回复文本消息
                case 'image':$result=$this->reciveImg($postObj);break;//回复图片消息
                case 'voice':$result=$this->reciveVoice($postObj);break;//回复语音消息
                case 'video':$result=$this->reciveVideo($postObj);break;//回复视频消息
                case 'location':$result=$this->reciveLocation($postObj);break;//回复地理位置消息
            }
            echo $result;
        }else{
            echo "";
            exit;
        }
    }

    public function reviveEvent($postObj)
    {
        $content='';
        switch ($postObj->Event){
            case 'subscribe':$content='欢迎关注0zero公众号';break;
            case 'unsubscribe':$content='感谢您关注测试公众号，希望下次可以再次关注';break;
            case 'CLICK':
                switch ($postObj->EventKey){
                    case 'qrcode':$content=array('qrcode'=>'P8vLRH6pJFWzS15DjxCtsov0iFnznXFnj3Nx_WjlVwe2o2Ca8pQP05JbyeQqCKcq');break;
                    default:$content=$postObj->EventKey;
                }
                break;
        }
        if (is_array($content)){
            $content=$this->transmitImage($postObj,$content['qrcode']);
        }else{
            $content=$this->transmitText($postObj,$content);
        }
        return $content;
    }

    public function reciveText($postObj)
    {
        $contennt=$postObj->Content;
        if ($contennt=='程序员'){
            $result=$this->transmiNews($postObj,$contennt);
            return $result;
        }
        if($contennt=='图片'){
            $url='http://'.$_SERVER['SERVER_NAME'].'/jssdk/jssdk.html';
            $result=$this->transmitText($postObj,$url);
            return $result;
        }
        if($contennt=='分享'){
            $url='http://'.$_SERVER['SERVER_NAME'].'/jssdk/share.html';
            $result=$this->transmitText($postObj,$url);
            return $result;
        }
        if($contennt=='录音'){
            $url='http://'.$_SERVER['SERVER_NAME'].'/jssdk/audio.html';
            $result=$this->transmitText($postObj,$url);
            return $result;
        }
        if($contennt=='授权'){
            $url='http://'.$_SERVER['SERVER_NAME'].'/scope/scope.html';
            $contennt='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx54abfd3dac845fab&redirect_uri='.urlencode($url).'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
            $result=$this->transmitText($postObj,$contennt);
            return $result;
        }
        $result=$this->transmitText($postObj,$contennt);
        return $result;
    }

    public function reciveImg($postObj)
    {
        $content = $postObj->MediaId;
        $result=$this->transmitImage($postObj,$content);
        return $result;
    }

    public function reciveVoice($postObj)
    {
        $content=$postObj->MediaId;
        $result=$this->transmiVoice($postObj,$content);
        return $result;
    }

    public function reciveLocation($postObj)
    {
        $content=$postObj->Label;
        $result=$this->transmiLocation($postObj,$content);
        return $result;
    }

    public function reciveVideo($postObj)
    {
        $content=array('MediaId'=>$postObj->media_id,'Title'=>'木头人','Description'=>'1231231231234122435457798');
        $result=$this->transmiVedio($postObj,$content);
        return $result;
    }
    private function transmitText($postObj, $content)
    {
        if (!isset($content) || empty($content)){
            return "";
        }
        $xmlTpl = "<xml>
                       <ToUserName><![CDATA[%s]]></ToUserName>
                       <FromUserName><![CDATA[%s]]></FromUserName>
                       <CreateTime>%s</CreateTime>
                       <MsgType><![CDATA[text]]></MsgType>
                       <Content><![CDATA[%s]]></Content>
                   </xml>";
        $result = sprintf($xmlTpl, $postObj->FromUserName, $postObj->ToUserName, time(), $content);
        return $result;
    }
    public function transmitImage($postObj, $content)
    {
        $xmlTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[image]]></MsgType>
                        <Image>
                            <MediaId><![CDATA[%s]]></MediaId>
                        </Image>
                   </xml>";
        $result = sprintf($xmlTpl, $postObj->FromUserName, $postObj->ToUserName, time(),$content);
        return $result;
    }

    public function transmiVoice($postObj,$content)
    {
        $xmlTpl="<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[voice]]></MsgType>
                    <Voice>
                        <MediaId><![CDATA[%s]]></MediaId>
                    </Voice>
                </xml>";
        $result=sprintf($xmlTpl,$postObj->FromUserName,$postObj->ToUserName,time(),$content);
        return $result;
    }
    public function transmiLocation($postObj,$content)
    {
        $xmlTpl="<xml>
                       <ToUserName><![CDATA[%s]]></ToUserName>
                       <FromUserName><![CDATA[%s]]></FromUserName>
                       <CreateTime>%s</CreateTime>
                       <MsgType><![CDATA[text]]></MsgType>
                       <Content><![CDATA[%s]]></Content>
                   </xml>";
        $result=sprintf($xmlTpl,$postObj->FromUserName,$postObj->ToUserName,time(),$content);
        return $result;
    }

    public function transmiVedio($postObj,$content)
    {
        $xmlTpl="<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[video]]></MsgType>
                    <Video>
                        <MediaId><![CDATA[%s]]></MediaId>
                        <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    </Video> 
                </xml>";
        $result=sprintf($xmlTpl,$postObj->FromUserName,$postObj->ToUserName,time(),$content['MediaId'],$content['Title'],$content['Description']);
        return $result;
    }

    public function transmiNews($postObj,$content)
    {
        $xmlTpl="<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>2</ArticleCount>
                    <Articles>
                        <item>
                            <Title><![CDATA[程序员的悲伤]]></Title>
                            <Description><![CDATA[作为创作性的工作者，程序员最悲哀的事就是：很少有人能真正欣赏你的作品。]]></Description>
                            <PicUrl><![CDATA[http://mmbiz.qpic.cn/mmbiz_jpg/6w9rjNPndgComM0XIqtbK5koTXswvwEhwbryfk05u6FcMhnkRFyOKh0dWfiaOP8c1CtQthULOrcX4nh4pO6wia7A/0]]></PicUrl>
                            <Url><![CDATA[https://www.zhihu.com/question/50327690?from=profile_question_card]]></Url>
                        </item>
                        <item>
                            <Title><![CDATA[程序员的工资]]></Title>
                            <Description><![CDATA[2016年夏季互联网高端人才流动报告]]></Description>
                            <PicUrl><![CDATA[http://img2.niushe.com/upload/201304/19/14-22-31-71-26144.jpg]]></PicUrl>
                            <Url><![CDATA[https://www.zhihu.com/question/31687394]]></Url>
                        </item>
                    </Articles>
                </xml>";
        $result=sprintf($xmlTpl,$postObj->FromUserName,$postObj->ToUserName,time());
        return $result;
    }
}
