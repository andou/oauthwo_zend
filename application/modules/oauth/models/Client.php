<?php
/**
 * 
 * Client.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements a Client Model Object
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */

class Oauth_Model_Client {

    /**
     * This client's id
     * 
     * @var string
     */
    protected $_id;
    
    /**
     * This client's name
     * 
     * @var string
     */
    protected $_name;
    
    /**
     * This client's secret
     * 
     * @var string
     */
    protected $_secret;
    
    /**
     * This client's redirect uri
     * 
     * @var string
     */
    protected $_redirectUri;
    
    /**
     * This client's type
     * 
     * @var string
     */
    protected $_type;

    public function setId($id) {
        $this->_id = (string) $id;
        return $this;
    }

    public function getId() {
        return $this->_id;
    }

    public function setName($name) {
        $this->_name = (string) $name;
        return $this;
    }

    public function getName() {
        return $this->_name;
    }

    public function setSecret($secret) {
        $this->_secret = (string) $secret;
        return $this;
    }

    public function getSecret() {
        return $this->_secret;
    }

    public function hasSecret() {
        return isset($this->_secret);
    }

    public function setRedirectUri($redirectUri) {
        $this->_redirectUri = (string) $redirectUri;
        return $this;
    }

    public function getRedirectUri() {
        return $this->_redirectUri;
    }

    public function setType($type) {
        $this->_type = (string) $type;
        return $this;
    }

    public function getType() {
        return $this->_type;
    }

    public function checkRedirectUri($redirectUri) {
        return $redirectUri === $this->getRedirectUri();
    }

    public function isAuthorized($type) {
        switch ($type) {
            case RESPONSE_TYPE_CODE:
            case GRANT_TYPE_AUTHORIZATION_CODE:
            case GRANT_TYPE_PASSWORD:
            case GRANT_TYPE_CLIENT_CREDENTIAL:
            case GRANT_TYPE_REFRESH_TOKEN:
                return $this->getType() === CLIENT_TYPE_WEB;
                break;
        }

        return true;
    }

}