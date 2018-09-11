<?php
    require_once("helper.php");

    class Data {
        private $_hp;
        private $_tp_id = [];

        public function __construct(){
            $this->_hp = new Helper();
            $this->_hp->authorization();

            if (empty($_POST['id'])){
                die();
            }
        }

        private function add_text_pole($type_string){
            $data = array (
                'add' =>
                    array (
                        0 =>
                            array (
                                'name' => 'tp',
                                'type' => '1',
                                'element_type' => $_POST['obj_type'],
                                'origin' => rand(100, 900)
                            ),
                    ),
            );
            $this->_hp->send("fields", $data);
            $this->get_text_pole($type_string);
        }

        private function get_text_pole($type_string){
            $response = $this->_hp->ask("account?with=custom_fields");

            $flag = false;

            foreach ($response['_embedded']['custom_fields'][$type_string] as $key => $value) {
                if($value['field_type'] == 1 && $value['name'] === "tp") {
                    $this->_tp_id = $key;
                    $flag = true;
                }
            }

            if($flag == false){
                $this->add_text_pole($type_string);
            }
            sleep(1);
        }

        private function generate_query_data(){
            $int_date = strtotime(date("Y-m-d H:i:s"));
            $data = array (
                'update' =>
                    array (
                        0 =>
                            array (
                                'id' => $_POST['id'],
                                'updated_at' => $int_date,
                                'custom_fields' =>
                                    array (
                                        0 =>
                                            array (
                                                'id' => $this->_tp_id,
                                                'values' =>
                                                    array (
                                                        0 =>
                                                            array (
                                                                'value' => $_POST['text'],
                                                            ),
                                                    ),
                                            ),
                                    ),
                            ),
                    ),
            );

            return $data;
        }

        public function update_object(){
            $type = $_POST['obj_type'];

            switch ($type) {
                case 1:
                    $this->get_text_pole("contacts");
                    $data = $this->generate_query_data("contacts");
                    $this->_hp->send("contacts", $data);
                    break;
                case 3:
                    $this->get_text_pole("companies");
                    $data = $this->generate_query_data("companies");
                    $this->_hp->send("companies", $data);
                    break;
                case 12:
                    $this->get_text_pole("customers");
                    $data = $this->generate_query_data("customers");
                    $this->_hp->send("customers", $data);
                    break;
                case 2:
                    $this->get_text_pole("leads");
                    $data = $this->generate_query_data("leads");
                    $this->_hp->send("leads", $data);
                    break;
            }

            header("http://localhost/");
        }
    }
    $d = new Data();
    $d->update_object();
?>