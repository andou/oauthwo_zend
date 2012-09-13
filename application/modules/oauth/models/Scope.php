<?php
/**
 * 
 * Scope.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements a Scope Model Object
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Model_Scope{
       
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


