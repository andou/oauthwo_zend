<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JWT_Producer
 *
 * @author andou
 */
abstract class Oauth_Builder_JWT{

    protected function get_base64_encode($string) {
        //simply use the php built-in function
        return base64_encode($string);
    }

    public function set_header($key, $value) {
        $this->header[$key] = $value;
    }

    abstract function get_token($payload);
}

?>
