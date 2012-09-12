<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResourceServer
 *
 * @author andou
 */
class Oauth_Model_ResourceServer{
    
        
    protected $_id;
    protected $_secret;
    protected $_type;    
    protected $_name;
    protected $_uri;
    
    public function setName($name) {
        $this->_name = (string) $name;
        return $this;
    }

    public function getName() {
        return $this->_name;
    }
    
    
    
    public function setId($id) {
        $this->_id = (string) $id;
        return $this;
    }
    
    public function getId(){
        return $this->_id;
    }
    
    
    public function setSecret($secret) {
        $this->_secret = (string) $secret;
        return $this;
    }
    
    public function getSecret(){
        return $this->_secret;
    }
    
    public function setType($type){
        $this->_type = (string) $type;
        return $this;       
    }
    
    public function getType(){
        return $this->_type;
    }
    
    
    public function setUri($uri){
        $this->_uri = (string) $uri;
        return $this;       
    }
    
    public function getUri(){
        return $this->_uri;
    }  
    
    
}
