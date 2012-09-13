<?php
/**
 * 
 * JWE.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Builder class to create JSON Encrypted Tokens
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Builder_JWE extends Oauth_Builder_JWT {

    
    /**
     * The message header
     *
     * @var string
     */
    protected $header;
    
    /**
     * The encrypted used key. Void in this implementation
     *
     * @var string
     */
    protected $encrypted_key;
    
    /**
     * Crypted plaintext
     *
     * @var string
     */
    protected $ciphertext;
    
    /**
     * Integrity check value - NOT YET IMPLEMENTED!!
     *
     * @var string
     */
    protected $integrity_value;
    
    /**
     * Shared key used to cipher the plaintext
     *
     * @var string
     */
    private $key;

    /**
     * Constructor class. Sets some default values for this implementation
     * 
     */
    public function __construct() {
        $this->header = array();
        //direct encryption using a shared key
        $this->set_header('alg', 'dir');
        //AES in CBC mode with PKCS #5 padding using 256 bit keys
        $this->set_header('enc', 'A256CBC');
        $this->set_header('typ', 'JWT');
        //void because we use direct encryption with shared key        
        $this->encrypted_key = "";
    }
    
    /**
     * Sets the key used to cipher
     *
     * @param string $shared_key 
     */
    public function set_key($shared_key){        
        $this->key=$shared_key;
    }
    
    /**
     * Builds a JWE ciphering a plaintext value
     *
     * @param string $plaintext
     * @return string
     */
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
