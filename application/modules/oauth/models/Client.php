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

    /**
     * Sets this client's id
     * 
     * @param string $id
     * @return Oauth_Model_Client 
     */
    public function setId($id) {
        $this->_id = (string) $id;
        return $this;
    }

    /**
     * Returns this client's id
     *
     * @return string
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * Sets this client's name
     *
     * @param string $name
     * @return Oauth_Model_Client 
     */
    public function setName($name) {
        $this->_name = (string) $name;
        return $this;
    }

    /**
     * Return this client's name
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Sets this client's secret
     *
     * @param string $secret
     * @return Oauth_Model_Client 
     */
    public function setSecret($secret) {
        $this->_secret = (string) $secret;
        return $this;
    }

    /**
     * Return this client's secret
     * 
     * @return string
     */
    public function getSecret() {
        return $this->_secret;
    }

    /**
     * Checks wherever this client has a secret
     * @return boolean
     */
    public function hasSecret() {
        return isset($this->_secret);
    }

    /**
     * Sets this client's redirect uri
     *
     * @param string $redirectUri
     * @return Oauth_Model_Client 
     */
    public function setRedirectUri($redirectUri) {
        $this->_redirectUri = (string) $redirectUri;
        return $this;
    }

    /**
     * Return this client's redirect URI
     * @return string
     */
    public function getRedirectUri() {
        return $this->_redirectUri;
    }

    /**
     * Sets this client's type
     *
     * @param string $type
     * @return Oauth_Model_Client 
     */
    public function setType($type) {
        $this->_type = (string) $type;
        return $this;
    }

    /**
     * Return this client's type
     *
     * @return string
     */
    public function getType() {
        return $this->_type;
    }

    /**
     * Checks if $redirectUri is compatible whith this client's redirect URI
     *
     * @param string $redirectUri
     * @return boolean
     */
    public function checkRedirectUri($redirectUri) {
        return $redirectUri === $this->getRedirectUri();
    }

    /**
     * Checks if this client is authorized to make a $type grant request
     * to the Authorization Server. By now only a web client can make 
     * requests. The other clients can't.
     *
     * @param string $type
     * @return boolean authorized or not 
     */
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