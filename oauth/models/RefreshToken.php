<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthorizationCode
 *
 * @author andou
 */
class Oauth_Model_RefreshToken{

    protected $_code;
    /**
     *
     * @var Oauth_Model_Client
     */
    protected $_client;
    protected $_scopes;
    protected $_resource_owner_id;
    protected $_created;
    
    
    public function setCode($code) {
        $this->_code = (string) $code;
        return $this;
    }

    public function getCode() {
        return $this->_code;
    }

    public function setClient(Oauth_Model_Client $client) {
        $this->_client = $client;
        return $this;
    }

    public function getClient() {
        return $this->_client;
    }

    public function getClientId() {
        return $this->_client->getId();
    }
    
    public function setScopes($scopes) {
        $this->_scopes = (string) $scopes;
        return $this;
    }

    public function getScopes() {
        return $this->_scopes;
    }
        
    public function setResourceOwnerId($id){
        $this->_resource_owner_id = (string)$id;
        return $this;
    }
    
    public function getResourceOwnerId(){
        return $this->_resource_owner_id;
    }
    
    public function getCreated(){
        return $this->_created;
    }
    
    public function setCreated($ts){
        $this->_created = strtotime($ts);
        return $this;
    }
    
    
    public function checkTimeValidity($ts){                
        $valid_until = $this->getCreated() + REFRESH_TOKEN_VALIDITY;        
        return $valid_until > $ts;        
    }
    
    public function checkClient(Oauth_Model_Client $client){
        return $this->getClientId() === $client->getId();
    }
    
    
    

}

?>
