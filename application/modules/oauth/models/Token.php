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

    protected $_type;
    protected $_code;
    protected $_expiredate;

    public function setCode($code) {
        $this->_code = (string) $code;
        return $this;
    }

    public function getCode() {
        return $this->_code;
    }

    public function setType($type) {
        $this->_type = (string) $type;
        return $this;
    }

    public function getType() {
        return $this->_type;
    }

    public function setExpireDate($ts) {
        $this->_expiredate = $ts;
        return $this;
    }

    public function getExpireDate() {
        return $this->_expiredate;
    }

}
