<?php
    $menu=new Menu();
    $menu->setMenu();
    class Menu{
        private $appId='wx54abfd3dac845fab';
        private $appsecret='addd017afa6d5dd22f48f69a638b9b0c';
        public function https_request($url, $data = null)
        {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            if (!empty($data)){
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
        public function setMenu(){
            $access_token=$this->getAccessToken();
            $json='{
                "button":[
                    {
                        "name":"Web",
                        "sub_button":[
                            {
                                "name":"HTML",
                                "type":"click",
                                "key":"html"
                            },
                            {
                                "name":"CSS",
                                "type":"click",
                                "key":"css"
                            },
                            {
                                "name":"javaScript",
                                "type":"click",
                                "key":"js"
                            }                                                      
                        ]
                    },
                    {
                        "name":"Php",
                        "sub_button":[
                            {
                                "name":"Linux",
                                "type":"click",
                                "key":"linux"
                            },
                            {
                                "name":"Php",
                                "type":"click",
                                "key":"php"
                            },
                            {
                                "name":"MySql",
                                "type":"click",
                                "key":"mysql"
                            },
                            {                                                                           
                                "name":"Tp",
                                "type":"click",
                                "key":"tp"
                            },
                            {                                                                           
                                "name":"Yii",
                                "type":"click",
                                "key":"yii"
                            }                              
                        ]
                    },   
                    {
                        "name":"小玩意",
                        "sub_button":[
                            {
                                "name":"打赏",
                                "type":"click",
                                "key":"money"
                            },                        
                            {
                                "name":"历史记录",
                                "type":"click",
                                "key":"more"
                            }, 
                            {
                                "name":"二维码",
                                "type":"click",
                                "key":"qrcode"
                            }                           
                        ]
                    }                                    
                ]
            }';
            $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
            $result=$this->https_request($url,$json);
            var_dump($result);
        }
    }