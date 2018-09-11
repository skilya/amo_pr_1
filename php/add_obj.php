<?php
    require_once("helper.php");
    set_time_limit ( 3600 );

    class Data {
        private $_count;            // Количество создаваемых сущностей.
        private $_hp;               // Объект класса Helper.
        private $_company_id = [];  // Массив хранящий ID компаний.
        private $_contact_id = [];  // Массив хранящий ID контактов.
        private $_msl = [];         // Массив хранящий ID элементов мультисписка.
        private $_ms_id;            // ID мультисписка.
        private $_rand_arr_id = []; // Массив хранящий случайный набор ID мультисписка.


        public function __construct(){
            if (empty($_POST['count'])){
                die();
            }

            $this->_count = $_POST["count"];
            $this->_hp = new Helper();
            $this->_hp->authorization();
        }

        private function gen_companies($count) {
            for ($i = 0; $i < $count; $i++) {
                $arr[$i][name] = "company ".$i;
            }
            $result = array ('add' => $arr);
            return $result;
        }
        private function send_companies($count){
            $data = $this->gen_companies($count);
            $response = $this->_hp->send("companies", $data);
            for($i = 0; $i < $this->_count; $i++) {
                $this->_company_id[$i] = $response["_embedded"]["items"][$i]["id"];
            }
        }

        private function gen_contacts($count, $iterat){
            for ($i = 0; $i < $count; $i++) {
                $w = 500 * $iterat + $i;
                $arr[$i]['name'] = "contact ".$w;
                $arr[$i]['company_id'] = $this->_company_id[$i];
            }
            $result = array ('add' => $arr);
            return $result;
        }
        private function send_contacts($count, $iterat){
            $data = $this->gen_contacts($count, $iterat);
            $response = $this->_hp->send("contacts", $data);
            for($i = 0; $i < $this->_count; $i++) {
                $this->_contact_id[$i] = $response["_embedded"]["items"][$i]["id"];
            }
        }

        private function gen_leads($count){
            for ($i = 0; $i < $count; $i++) {
                $arr[$i]['name'] = "lead ".$i;
                $arr[$i]['company_id'] = $this->_company_id[$i];
                $arr[$i]['contacts_id'][0] = $this->_contact_id[$i];
            }
            $result = array ('add' => $arr);
            return $result;
        }
        private function send_leads($count){
            $data = $this->gen_leads($count);
            $this->_hp->send("leads", $data);
        }

        private function gen_customers($count){
            $int_date = strtotime(date("Y-m-d H:i:s"));

            for ($i = 0; $i < $count; $i++) {
                $arr[$i]['name'] = "customer ".$i;
                $arr[$i]['next_date'] = $int_date;
                $arr[$i]['company_id'] = $this->_company_id[$i];
                $arr[$i]['contacts_id'][0] = $this->_contact_id[$i];
            }
            $result = array ('add' => $arr);
            return $result;
        }
        private function send_customers($count){
            $data = $this->gen_customers($count);
            $this->_hp->send("customers", $data);
        }

        private function add_ms(){
            $data = array (
                'add' =>
                    array (
                        0 =>
                            array (
                                'name' => 'ms',
                                'type' => '5',
                                'element_type' => "1",
                                'origin' => '0000',
                                'enums' =>
                                    array (
                                        0 => 'персик',
                                        1 => 'банан',
                                        2 => 'абрикос',
                                        3 => 'кокос',
                                        4 => 'кокс',
                                        5 => 'салат',
                                        6 => 'булка',
                                        7 => 'чай',
                                    ),
                            ),
                    ),
            );
            $this->_hp->send("fields", $data);
            $this->get_ms();
        }

        private function get_ms(){
            $flag = false;
            $response = $this->_hp->ask("account?with=custom_fields");
            foreach ($response['_embedded']['custom_fields']['contacts'] as $key => $value) {
                if($value['field_type'] == 5 && $value['name'] === "ms") {
                    $flag = true;
                    $this->_ms_id = $key;
                }
            }
            if($flag != true) {
                $this->add_ms();
            }
        }

        private function get_cont_list($iterat){
            $offset = $iterat * 500;
            $response = $this->_hp->ask("contacts/?limit_rows=500&limit_offset=".$offset);
            $c = count($response['_embedded']['items']);
            for($i = 0; $i < $c; $i++) {
                $this->_contact_id[$iterat][] = $response['_embedded']['items'][$i]['id'];
            }
        }

        private function get_ms_list(){
            $response = $this->_hp->ask("account?with=custom_fields");
            foreach ($response['_embedded']['custom_fields']['contacts'][$this->_ms_id]['enums'] as $key => $value) {
                $this->_msl[] = $key;
            }
        }

        private function ms_arr_rand(){
            $msl_count = count($this->_msl);
            for($j = 0; $j < 4; $j++) {
                $this->_rand_arr_id[$j] = $this->_msl[rand(0, $msl_count)];
            }
        }

        private function contacts_update_gen($iterat){
            $int_date = strtotime(date("Y-m-d H:i:s"));
            for ($i = 0; $i < 500; $i++) {
                if(empty($this->_contact_id[$iterat][$i])){
                    break;
                }

                $arr[$i]['id'] = $this->_contact_id[$iterat][$i];
                $arr[$i]['updated_at'] = $int_date;
                $arr[$i]['custom_fields'][0]['id'] = $this->_ms_id;
                $arr[$i]['custom_fields'][0]['values'] = $this->_rand_arr_id;
            }
            $result = array ('update' => $arr);
            return $result;
        }

        private  function contacts_update(){
            $i = 0;
            $this->_contact_id = array();

            while(true) {
                $this->get_cont_list($i);
                if (count($this->_contact_id[$i]) > 0) {
                    $i++;
                } else {
                    break;
                }
            }

            for ($j = 0; $j < count($this->_contact_id[$j]); $j++) {
                $data = $this->contacts_update_gen($j);
                $this->_hp->send("contacts", $data);
            }
        }

        public function add(){
            $this->get_ms();
            $this->get_ms_list();
            $this->ms_arr_rand();

            $fcont = $this->_count / 500;
            $fcont = intval($fcont);
            $ocont = $this->_count % 500;

            if($fcont > 0){
                for($i = 0; $i < $fcont; $i++){
                    $this->send_companies(500);
                    $this->send_contacts(500, $i);
                    $this->send_leads(500);
                    $this->send_customers(500);
                }
                if($ocont > 0){
                    $this->send_companies($ocont);
                    $this->send_contacts($ocont, $i);
                    $this->send_leads($ocont);
                    $this->send_customers($ocont);
                }
            } else {
                $this->send_companies($ocont);
                $this->send_contacts($ocont, 0);
                $this->send_leads($ocont);
                $this->send_customers($ocont);
            }
            $this->contacts_update();

            header("Location: http://localhost/");
        }
        public function update(){
            $this->get_ms();
            $this->get_ms_list();
            $this->ms_arr_rand();
            $this->contacts_update();

            header("Location: http://localhost/");
        }
    }

    $d = new Data();
    $d->add();
?>