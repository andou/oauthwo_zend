<?php
/**
 * 
 * RefreshToken.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements a REfresh Token Model Object
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Model_RefreshToken{

    /**
     * The code of this refresh token object, i.e. what is send to the client
     *
     * @var string
     */
    protected $_code;
    /**
     *
     * @var Oauth_Model_Client
     */
    protected $_client;
    
    /**
     * Scopes this token grants for
     * @var string
     */
    protected $_scopes;
    
    /**
     * Id of the granting resource owner
     * @var string
     */
    protected $_resource_owner_id;
    
    /**
     * Creation timestamp
     *
     * @var string
     */
    protected $_created;
    
    
    /**
     * Sets the this token's code
     *
     * @param String $code
     * @return Oauth_Model_AuthorizationCode 
     */
    public function setCode($code) {
        $this->_code = (string) $code;
        return $this;
    }

    
    /**
     * Returns the this token's code
     *
     * @return string 
     */
    public function getCode() {
        return $this->_code;
    }
    

 /**
     * Sets the issued at client
     * @param Oauth_Model_Client $client
     * @return Oauth_Model_AuthorizationCode 
     */
    public function setClient(Oauth_Model_Client $client) {
        $this->_client = $client;
        return $this;
    }

    /**
     * Returns the issued at client
     *
     * @return Oauth_Model_Client
     */
    public function getClient() {
        return $this->_client;
    }
    
    /**
     * Returns the issued at client's id
     *
     * @return string
     */
    public function getClientId() {
        return $this->_client->getId();
    }
/**
     * Sets the granted scopes
     *
     * @param string $scopes
     * @return Oauth_Model_AuthorizationCode 
     */
    public function setScopes($scopes) {
        $this->_scopes = (string) $scopes;
        return $this;
    }

    /**
     * Return the scopes this code will grants
     *
     * @return string 
     */
    public function getScopes() {
        return $this->_scopes;
    }
        
        
    /**
     * Sets the granting resource owner id
     *
     * @param string $id
     * @return Oauth_Model_AuthorizationCode 
     */
    public function setResourceOwnerId($id){
        $this->_resource_owner_id = (string)$id;
        return $this;
    }
    
    /**
     * Returns the granting resource owner id
     *
     * @return string 
     */
    public function getResourceOwnerId(){
        return $this->_resource_owner_id;
    }
    
    /**
     * Returns the creation timestamp
     *
     * @return string 
     */    
    public function getCreated(){
        return $this->_created;
    }
    
    /**
     * Sets the creation timestamp
     *
     * @param string $ts
     * @return Oauth_Model_AuthorizationCode 
     */
    public function setCreated($ts){
        $this->_created = strtotime($ts);
        return $this;
    }
    
    /**
     * Check if this code is stil valid
     *
     * @param string $ts the timestamp
     * @return boolean valid or not
     */
    public function checkTimeValidity($ts){                
        $valid_until = $this->getCreated() + REFRESH_TOKEN_VALIDITY;        
        return $valid_until > $ts;        
    }
    
    /**
     * Checks the associated client
     *
     * @param Oauth_Model_Client $client
     * @return boolean valid or not
     */
    public function checkClient(Oauth_Model_Client $client){
        return $this->getClientId() === $client->getId();
    }
    
    
    

}
