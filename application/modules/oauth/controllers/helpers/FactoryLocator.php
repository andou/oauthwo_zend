<?php
/**
 * 
 * FactoryLocator.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Extends an abstract helper and is used by controllers to get factories
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Controller_Action_Helper_FactoryLocator extends Zend_Controller_Action_Helper_Abstract{
    
    
    /**
     * Returns the correct abstract factory to create a Token
     * 
     * @return Oauth_Factory_TokenProducer
     */
    public function getTokenFactory(){
        return new Oauth_Factory_TokenProducer();
    }
    
    
    /**
     * Returns the correct abstract factory to create a Token
     * 
     * @return Oauth_Factory_AuthorizationCodeConcreteFactory
     */
    public function getAuthorizationCodeFactory(){
        return new Oauth_Factory_AuthorizationCodeConcreteFactory();
    }
    
    /**
     * Returns the correct factory to create a refresh token
     *
     * @return Oauth_Factory_RefreshTokenConcreteFactory 
     */
    public function getRefreshtokenFactory(){
        return new Oauth_Factory_RefreshTokenConcreteFactory();
    }
    
    
}
