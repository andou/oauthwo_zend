<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JSONToken
 *
 * @author andou
 */
class Oauth_Factory_JETProducer extends Oauth_Factory_JWTProducer {

    //put your code here

    protected $header;
    protected $encrypted_key;
    protected $ciphertext;
    protected $integrity_value;
    
    private $key;

    public function __construct() {
        $this->header = array();
        //direct encryption using a shared key
        $this->set_header('alg', 'dir');
        //AES in CBC mode with PKCS #5 padding using 256 bit keys
        $this->set_header('enc', 'A256CBC');
        $this->set_header('typ', 'JWT');
        
        
        $this->encrypted_key = "";
        //$this->key = "asdpoaksd9a0093weka3p";
    }
    
    
    public function set_key($shared_key){        
        $this->key=$shared_key;
    }
    
    public function get_token($plaintext) {

        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
                        
        $this->set_header('iv', base64_encode($iv));

        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->key, $plaintext, MCRYPT_MODE_CBC, $iv);
        
        $enc_crypttext = $this->get_base64_encode($crypttext);
        
        $header = json_encode($this->header);
        $enc_header = $this->get_base64_encode($header);
        $enc_encrypted_key = $this->get_base64_encode($this->encrypted_key);
        
        return sprintf("%s.%s.%s",$enc_header,$enc_encrypted_key,$enc_crypttext);
    }
    

}

?>
