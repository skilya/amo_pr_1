<?php
    class Helper
    {
        private $_user;
        private $_link;
        private $_headers = [];

        public function __construct(){
            $this->_headers[] = "Accept: application/json";
            $this->_user = "rtrt";
        }

        public function send($_obj_type, $_data){
            $this->_link = "https://".$this->_user.".amocrm.ru/api/v2/".$_obj_type;

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_USERAGENT, "amoCRM-API-client-undefined/2.0");
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->_headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($_data));
            curl_setopt($curl, CURLOPT_URL, $this->_link);
            curl_setopt($curl, CURLOPT_HEADER,false);
            curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__)."/cookie.txt");
            curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__)."/cookie.txt");
            $out = curl_exec($curl);
            curl_close($curl);
            $result = json_decode($out,TRUE);
            sleep(1);

            return $result;
        }
        public function ask($_obj_type){
            $this->_link = 'https://'.$this->_user.'.amocrm.ru/api/v2/'.$_obj_type;

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_USERAGENT, "amoCRM-API-client-undefined/2.0");
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->_headers);
            curl_setopt($curl, CURLOPT_URL, $this->_link);
            curl_setopt($curl, CURLOPT_HEADER,false);
            curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__)."/cookie.txt");
            curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__)."/cookie.txt");
            $out = curl_exec($curl);
            curl_close($curl);
            $result = json_decode($out,TRUE);
            sleep(1);

            return $result;
        }
        private function send_auth_data(){
            $user=array(
                'USER_LOGIN'=>'iskoropad@team.amocrm.com',
                'USER_HASH'=>'b6d05a7890476c2acb8eaedbed7ebe5dcc956624'
            );
            $link='https://'.$this->_user.'.amocrm.ru/private/api/auth.php?type=json';

            $curl=curl_init();
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
            curl_setopt($curl,CURLOPT_URL,$link);
            curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
            curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($user));
            curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
            curl_setopt($curl,CURLOPT_HEADER,false);
            curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt');
            curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt');
            curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
            curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
            $out = curl_exec($curl);
            curl_close($curl);
            sleep(1);

            $result = json_decode($out,true);
            $result = $result['response'];

            return $result;
        }

        public function authorization(){
            $response = $this->send_auth_data();
            if(isset($response['auth'])); else {
                echo 'Авторизация не удалась';
            }
        }

        public function pre_print($data){
            echo "<pre>";
            print_r($data);
            echo "</pre>";
        }
    }
?>