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
                                "type":"view",
                                "key":"html",
                                 "url":"http://resume.0wade.win/"
                            },
                            {
                                "name":"CSS",
                                "type":"view",
                                "key":"css",
                                 "url":"http://resume.0wade.win/"
                            },
                            {
                                "name":"javaScript",
                                "type":"view",
                                "key":"js",
                                 "url":"http://resume.0wade.win/"
                            }                                                      
                        ]
                    },
                    {
                        "name":"Php",
                        "sub_button":[
                            {
                                "name":"Linux",
                                "type":"view",
                                "key":"linux",
                                 "url":"http://resume.0wade.win/"
                            },
                            {
                                "name":"Php",
                                "type":"view",
                                "key":"php",
                                 "url":"http://resume.0wade.win/"
                            },
                            {
                                "name":"MySql",
                                "type":"view",
                                "key":"mysql",
                                 "url":"http://resume.0wade.win/"
                            },
                            {                                                                           
                                "name":"Tp",
                                "type":"view",
                                "key":"tp",
                                "url":"http://resume.0wade.win/"
                            },
                            {                                                                           
                                "name":"Yii",
                                "type":"view",
                                "key":"yii",
                                 "url":"http://resume.0wade.win/"
                            }                              
                        ]
                    },   
                    {
                        "name":"小玩意",
                        "sub_button":[
                            {
                                "type": "scancode_push", 
                                "name": "扫码推事件", 
                                "key": "rselfmenu_0_1"
                            },
                            {
                                "type": "scancode_waitmsg", 
                                "name": "扫码带提示", 
                                "key": "rselfmenu"
                            },   
                            {                                                    
                                "name": "发送位置", 
                                "type": "location_select", 
                                "key": "rselfmenu_2_0"
                            },
                            {
                                "type": "pic_photo_or_album", 
                                "name": "拍照或者相册发图", 
                                "key": "rselfmenu_1_1"
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