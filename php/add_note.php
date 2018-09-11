<?php
    require_once("helper.php");

    class Data{
        private $_hp;

        public function __construct(){
            $this->_hp = new Helper();
            $this->_hp->authorization();

            if (empty($_POST['id'])){
                die();
            }
        }

        private function generate_note(){
            $data = array (
                'add' =>
                    array (
                        0 =>
                            array (
                                'element_id' => $_POST['id'],
                                'element_type' => $_POST['obj_type'],
                                'note_type' => 4,
                                'text' => $_POST['text'],
                            ),
                    ),
            );

            return $data;
        }
        private function generate_call(){
            $data = array (
                'add' =>
                    array (
                        0 =>
                            array (
                                'element_id' => $_POST['id'],
                                'element_type' => $_POST['obj_type'],
                                'note_type' => 11,
                                'params' =>
                                    array (
                                        'PHONE' => $_POST['text'],
                                    ),
                            ),
                    ),
            );

            return $data;
        }

        public function update_object(){
            $type = $_POST['note_type'];

            switch ($type) {
                case 4:
                    $data = $this->generate_note();
                    $this->_hp->send("notes", $data);
                    break;
                case 6:
                    $data = $this->generate_call();
                    $this->_hp->send("notes", $data);
                    break;
            }

            header("Location: http://localhost/?res=");
        }
    }
    $d = new Data();
    $d->update_object();
?>