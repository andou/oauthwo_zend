<?php
/**
 * 
 * ResourceOwner.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements a Resource Owner Model Object
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Model_ResourceOwner{
    
    /**
     * This Resource Owner's ID
     * 
     * @var string
     */
    protected $_id;
    
    /**
     * The Resource Owner Identity Equivalence Class in an associative array
     * 
     * Array's structure is
     * resource_server_id -> resource_owner_reference 
     * 
     * @var array
     */
    protected $_references;
    
    
    /**
     * Sets this Resource Owner's id
     *
     * @param string $id
     * @return Oauth_Model_ResourceOwner 
     */
    public function setId($id) {
        $this->_id = (string) $id;
        return $this;
    }

    /**
     * Returns this Resource Owner's Id
     *
     * @return string
     */
    public function getId() {
        return $this->_id;
    }
    
    
    /**
     * Returns this Resource Owner reference for a specific resource server ID
     *
     * @param string $resource_server
     * @return string or FALSE
     */
    public function getReference($resource_server) {
        if(array_key_exists($resource_server, $this->_references)){
            return $this->_references[$resource_server];
        }
        return FALSE;
    }
    
    /**
     * Sets the Resource Owner Identity Equivalence Class
     *
     * @param array $_references
     * @return Oauth_Model_ResourceOwner 
     */
    public function setReferences($_references){
        $this->_references = (array) $_references;
        return $this;
    }  
}