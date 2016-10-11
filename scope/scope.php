<?php
    header('Content-type:text/json;charset=utf-8');
     function https_request($url, $data = null)
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
    if ($_POST){
        $code=$_POST['code'];
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx54abfd3dac845fab&secret=addd017afa6d5dd22f48f69a638b9b0c&code='.$code.'&grant_type=authorization_code';
        $array=json_decode(https_request($url));
        $openId=$array->openid;
        $accessToken=$array->access_token;
        $url='https://api.weixin.qq.com/sns/userinfo?access_token='.$accessToken.'&openid='.$openId.'&lang=zh_CN';
        $json=https_request($url);
        echo $json;
    }