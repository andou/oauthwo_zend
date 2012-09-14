<?php
/**
 * 
 * JWT.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Abstract class which resemble a JSON Web Token
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
abstract class Oauth_Builder_JWT{

    /**
     * Helper function to base64 encode a string. 
     * In case we want to change it or add trim functionality/ecc.
     *
     * @param string $string
     * @return string 
     */
    protected function get_base64_encode($string) {
        //simply use the php built-in function
        return base64_encode($string);
    }

    /**
     * Sets the $key header parameter to $value
     *
     * @param string $key
     * @param string $value 
     */
    public function set_header($key, $value) {
        $this->header[$key] = $value;
    }

    /**
     * Each JWT extending class should have a function to retrieve a token 
     * given a payload.
     * 
     */
    abstract function get_token($payload);
}
