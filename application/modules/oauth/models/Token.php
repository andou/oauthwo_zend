<?php
/**
 * 
 * Token.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements a Token Model Object
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Model_Token {

    /**
     * This token's type - OAuthwo uses bearer tokens
     *
     * @var string
     */
    protected $_type;
    
    /**
     * This token's code value
     * 
     * @var string 
     */
    protected $_code;
    
    /**
     * This token's expiration date
     *
     * @var string
     */
    protected $_expiredate;

    /**
     * Sets this token's code value
     *
     * @param string $code
     * @return Oauth_Model_Token 
     */
    public function setCode($code) {
        $this->_code = (string) $code;
        return $this;
    }

    /**
     * Returns this token's code value
     *
     * @return string
     */
    public function getCode() {
        return $this->_code;
    }

    /**
     * Sets the token type
     *
     * @param string $type
     * @return Oauth_Model_Token 
     */
    public function setType($type) {
        $this->_type = (string) $type;
        return $this;
    }

    /**
     * Returns the token type
     *
     * @return string
     */
    public function getType() {
        return $this->_type;
    }

    /**
     * Sets this token's expiration date
     *
     * @param string $ts
     * @return Oauth_Model_Token 
     */
    public function setExpireDate($ts) {
        $this->_expiredate = $ts;
        return $this;
    }

    /**
     * Returns this token's expiration date
     *
     * @return string
     */
    public function getExpireDate() {
        return $this->_expiredate;
    }

}
