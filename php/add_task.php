<?php
    require_once("helper.php");

    class Data
    {
        private $_hp;

        public function __construct(){
            if (empty($_POST['id']) || empty($_POST['resp_id'])){
                die();
            }

            $this->_hp = new Helper();
            $this->_hp->authorization();
        }

        private function generate_task(){
            $data = array (
                'add' =>
                    array (
                        0 =>
                            array (
                                'element_id' => $_POST['id'],
                                'element_type' => $_POST['obj_type'],
                                'complete_till' => $_POST['date'],
                                'task_type' => rand(1, 3),
                                'text' => $_POST['text'],
                                'responsible_user_id' => $_POST['resp_id']
                            ),
                    ),
            );
            return $data;
        }
        public function update_object(){
            $data = $this->generate_task();
            $this->_hp->send("tasks", $data);

            header("Location: http://localhost/");
        }
    }

    $d = new Data();
    $d->update_object();
?>