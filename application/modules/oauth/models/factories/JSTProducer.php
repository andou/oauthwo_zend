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
class Oauth_Factory_JSTProducer extends Oauth_Factory_JWTProducer{
    //put your code here
    
    protected $header;
    protected $key;
    
    
    public function __construct($private_key_location) {
        $this->header = array();
                
        $this->set_header('typ', 'JWT');
        $this->set_header('alg', 'RS256');
        
        $this->key=$private_key_location;
    }
    
    
    public function get_token($payload){        
        //we build the secured input
        $secured_input = $this->build_secured_input($payload);
        //we sign the secured input
        $signature = $this->sign_secured_input($secured_input);                             
        //we build the final token
        $signed_token = sprintf("%s.%s",$secured_input,$signature);
        //and return it       
        return $signed_token;
    }
      
    
    private function sign_secured_input($secured_input){
        //our future signature
        $signature = NULL;
        //our key location & signing algorithm
        $algo = "sha256";
        //let's read the key
        $fp = fopen($this->key, "r");
        $priv_key = fread($fp, 8192);
        fclose($fp);
        $pkeyid = openssl_get_privatekey($priv_key);
        //let's sign the secured input using openssl_sign
        openssl_sign($secured_input, $signature, $pkeyid, $algo);                
        openssl_free_key($pkeyid); 
        //return the base64 encoding of the signature.
        return $this->get_base64_encode($signature);
    }
    
    private function build_secured_input($payload){
        //base 64 encoding of the payload
        $enc_payload = $this->get_base64_encode($payload);
        //json encoding of the header
        $header = json_encode($this->header);
        //base 64 encoding of the jsonized header
        $enc_header = $this->get_base64_encode($header);
        //concat with a dot
        return sprintf("%s.%s",$enc_header,$enc_payload);
    }
    
    
    
    
}

?>
