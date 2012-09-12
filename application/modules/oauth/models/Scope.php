<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Scope
 *
 * @author andou
 */
class Oauth_Model_Scope{
    //put your code here
    
    
    protected $_name;
    protected $_description;
    protected $_resource_server;
    
    public function setName($name) {
        $this->_name = (string) $name;
        return $this;
    }

    public function getName() {
        return $this->_name;
    }
    
    public function setDescription($description) {
        $this->_description = (string) $description;
        return $this;
    }

    public function getDescription() {
        return $this->_description;
    }

    public function setResourceServer(Oauth_Model_ResourceServer $resource_server){
        $this->_resource_server = $resource_server;
        return $this;
    }
    
    public function getResourceServer() {
        return $this->_resource_server;
    }
    
    
}


