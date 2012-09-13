<?php
/**
 * 
 * ResourceServer.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements a Resource server Model Object
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Model_ResourceServer{
    
        
    /**
     * This server's ID
     * 
     * @var string
     */
    protected $_id;
    
    /**
     * This server's secret
     * 
     * @var string
     */
    protected $_secret;
    
    /**
     * This server's user referencing method
     * 
     * @var string
     */
    protected $_type;    
    
    /**
     * This server's name
     * 
     * @var string
     */
    protected $_name;
    
    /**
     * This server's enpoint URI
     * 
     * @var string
     */
    protected $_uri;
    
    /**
     * Sets this server's name
     *
     * @param string $name
     * @return Oauth_Model_ResourceServer 
     */
    public function setName($name) {
        $this->_name = (string) $name;
        return $this;
    }

    /**
     * Return this server's name
     * @return string
     */
    public function getName() {
        return $this->_name;
    }
    
    
    
    /**
     * Sets this server's id
     * 
     * @param string $id
     * @return Oauth_Model_ResourceServer 
     */
    public function setId($id) {
        $this->_id = (string) $id;
        return $this;
    }

    /**
     * Returns this server's id
     *
     * @return string
     */
    public function getId() {
        return $this->_id;
    }
    
    
    /**
     * Sets this server's secret
     *
     * @param string $secret
     * @return Oauth_Model_ResourceServer 
     */
    public function setSecret($secret) {
        $this->_secret = (string) $secret;
        return $this;
    }

    /**
     * Return this server's secret
     * 
     * @return string
     */
    public function getSecret() {
        return $this->_secret;
    }
    
    /**
     * Sets the user referencing for this server
     *
     * @param string $type
     * @return Oauth_Model_ResourceServer 
     */
    public function setType($type){
        $this->_type = (string) $type;
        return $this;       
    }
    
    /**
     * Returns the user referencing method of this server
     *
     * @return string
     */
    public function getType(){
        return $this->_type;
    }
    
    
    /**
     * Sets this server endpoint URI
     *
     * @param string $uri
     * @return Oauth_Model_ResourceServer 
     */
    public function setUri($uri){
        $this->_uri = (string) $uri;
        return $this;       
    }
    
    /**
     * Gets this server endpoint URI
     *
     * @return string
     */
    public function getUri(){
        return $this->_uri;
    }  
    
    
    
    
}

