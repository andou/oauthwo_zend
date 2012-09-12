<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FactoryLocator
 *
 * @author andou
 */
class Oauth_Controller_Action_Helper_FactoryLocator extends Zend_Controller_Action_Helper_Abstract{
    
    
    /**
     * Returns the correct abstract factory to create a Token
     * 
     * @return Oauth_Model_Factory_TokenAbstractFactory
     */
    public function getTokenFactory(){
        return new Oauth_Factory_TokenConcreteFactory();
    }
    
    
    /**
     * Returns the correct abstract factory to create a Token
     * 
     * @return Oauth_Model_Factory_TokenAbstractFactory
     */
    public function getAuthorizationCodeFactory(){
        return new Oauth_Factory_AuthorizationCodeConcreteFactory();
    }
    
    /**
     * Returns the correct factory to create a refresh token
     *
     * @return \Oauth_Factory_RefreshTokenConcreteFactory 
     */
    public function getRefreshtokenFactory(){
        return new Oauth_Factory_RefreshTokenConcreteFactory();
    }
    
    
}

?>
