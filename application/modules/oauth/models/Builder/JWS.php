<?php

/**
 * 
 * JWS.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Builder class to create JSON Web Signature objects
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Builder_JWS extends Oauth_Builder_JWT{
    
    /**
     * This JWS header
     *
     * @var string
     */
    protected $header;
    /**
     * private key location. Used to sign the secured payload
     *
     * @var string
     */
    protected $key;
    
    /**
     * Construct a JWS builder given a sign key location
     *
     * @param string $private_key_location 
     */
    public function __construct($private_key_location) {
        $this->header = array();
        //type is JSON Web Token
        $this->set_header('typ', 'JWT');
        //Signing algo is RSA with 256bit key
        $this->set_header('alg', 'RS256');
        
        $this->key=$private_key_location;
    }
    
    /**
     * Builds a JWS signing the secured input value
     *
     * @param string $plaintext
     * @return string
     */
    public function get_token($payload){        
        //we build the secured input
        $secured_input = $this->build_secured_input($payload);
        //we sign the secured input
        $signature = $this->sign($secured_input);                             
        //we build the final token
        $signed_token = sprintf("%s.%s",$secured_input,$signature);
        //and return it       
        return $signed_token;
    }
      
    /**
     * Signs the secured input. 
     *
     * @param string $secured_input
     * @return string
     */
    private function sign($secured_input){
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
    
    /**
     * Internal/helper function to construct the secured input, which is a 
     * "." concatenation of the base64 encoding of header json encoding and 
     * payload
     *
     * @param string $payload
     * @return string
     */
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
