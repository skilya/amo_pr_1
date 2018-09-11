<?php
    require_once("helper.php");

    class Data
    {
        private $_hp;

        public function __construct(){
            $this->_hp = new Helper();
            $this->_hp->authorization();
        }

        private function generate_query(){
            $int_date = strtotime(date("Y-m-d H:i:s"));
            $data = array (
                'update' =>
                    array (
                        0 =>
                            array (
                                'id' => $_POST['id'],
                                'updated_at' => $int_date,
                                'text' => 'closed',
                                'is_completed' => '1',
                            ),
                    ),
            );
            return $data;
        }
        public function update_object(){
            $data = $this->generate_query();
            $this->_hp->send("tasks", $data);

            header("Location: http://localhost/");
        }
    }

    $d = new Data();
    $d->update_object();
?>