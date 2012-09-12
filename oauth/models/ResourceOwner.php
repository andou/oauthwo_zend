<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResourceOwner
 *
 * @author andou
 */
class Oauth_Model_ResourceOwner{
    
    
    protected $_id;
    protected $_references;
    
    
    public function setId($id) {
        $this->_id = (string) $id;
        return $this;
    }

    public function getId() {
        return $this->_id;
    }
    
    
    public function getReference($resource_server) {
        if(array_key_exists($resource_server, $this->_references)){
            return $this->_references[$resource_server];
        }
        return FALSE;
    }
    
    public function setReferences($_references){
        $this->_references = (array) $_references;
        return $this;
    }
            
    
    
    
    
    
}

?>
