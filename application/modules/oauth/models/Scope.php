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
class Oauth_Model_Scope {

    /**
     * This scope's name
     *
     * @var string 
     */
    protected $_name;

    /**
     * This scope's description
     *
     * @var string 
     */
    protected $_description;

    /**
     * The resource server associated with this scope
     *
     * @var Oauth_Model_ResourceServer 
     */
    protected $_resource_server;

    /**
     * Sets this scope's name
     *
     * @param string $name
     * @return Oauth_Model_Scope 
     */
    public function setName($name) {
        $this->_name = (string) $name;
        return $this;
    }

    /**
     * Returns this scope's name
     * 
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Sets this scope's description
     *
     * @param string $description
     * @return Oauth_Model_Scope 
     */
    public function setDescription($description) {
        $this->_description = (string) $description;
        return $this;
    }

    /**
     * Returns this scope's description
     * 
     * @return string
     */
    public function getDescription() {
        return $this->_description;
    }

    /**
     * Sets this scope's associated resource server
     *
     * @param Oauth_Model_ResourceServer $resource_server
     * @return Oauth_Model_Scope 
     */
    public function setResourceServer(Oauth_Model_ResourceServer $resource_server) {
        $this->_resource_server = $resource_server;
        return $this;
    }

    /**
     * Returns this scope's associated resource server
     *
     * @return Oauth_Model_ResourceServer 
     */
    public function getResourceServer() {
        return $this->_resource_server;
    }

}

