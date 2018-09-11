<?php
/**
 * Created by PhpStorm.
 * User: iskoropad
 * Date: 06.09.2018
 * Time: 13:00
 */

class text_cl{
    private $_text;
    const LENGTH = 5;

    public function set_text($str){
        $this->text = $str;
    }

    public function get_substr(){
        slise($this->_text, self::LENGTH);
    }
}