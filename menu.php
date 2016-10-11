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
            require_once('./getAccessToken.php');
            $accessToken=new AccessToken();
            return $accessToken->getAccessToken();
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