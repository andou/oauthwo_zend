<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Defines the response type parameter name 
 */
define('RESPONSE_TYPE', 'response_type');

/**
 * Defines the response type parameter value code 
 */
define('RESPONSE_TYPE_CODE', 'code');

/**
 * Defines the response type parameter value token 
 */
define('RESPONSE_TYPE_TOKEN', 'token');

/**
 * Defines the client id parameter name 
 */
define('CLIENT_ID', 'client_id');

/**
 * Defines the redirect uri parameter name 
 */
define('REDIRECT_URI', 'redirect_uri');

/**
 * Defines the scope parameter name 
 */
define('SCOPE', 'scope');

/**
 * Defines the state parameter name 
 */
define('STATE', 'state');


define('CODE', 'code');
define('GRANT_TYPE', 'grant_type');


define('GRANT_TYPE_AUTHORIZATION_CODE', 'authorization_code');
define('GRANT_TYPE_PASSWORD', 'password');
define('GRANT_TYPE_CLIENT_CREDENTIAL', 'client_credentials');
define('GRANT_TYPE_REFRESH_TOKEN', 'refresh_token');




define('REFRESH_TOKEN', 'refresh_token');
define('USERNAME', 'username');
define('PASSWORD', 'password');


define('CLIENT_TYPE_WEB', 'web');
define('CLIENT_TYPE_USER_AGENT', 'user-agent');
define('CLIENT_TYPE_NATIVE', 'native');




//to be put in some config file
define('PRIVATE_SIGN_KEY_LOCATION','/var/www/oauthwo_zend/docs/key.pem');

define('ACCESS_TOKEN_VALIDITY',600000);//in seconds

define('AUTHORIZATION_CODE_VALIDITY',60000);//in seconds

define('REFRESH_TOKEN_VALIDITY',6000000);


/**
 * Description of Bootstrap
 *
 * @author andou
 */
class Oauth_Bootstrap extends Zend_Application_Module_Bootstrap {

    public function _initOauthRouting() {

        $ctrl = Zend_Controller_Front::getInstance();
        $router = $ctrl->getRouter();

        $route = new Zend_Controller_Router_Route('v2/oauth/:controller/:action',
                        array('module' => 'oauth', 'controller' => 'authorize', 'action' => 'index'));

        $router->addRoute('Oauth_module_route', $route);
    }

    protected function _initHelperPath() {
        Zend_Controller_Action_HelperBroker::addPath(
                APPLICATION_PATH . '/modules/oauth/controllers/helpers', 'Oauth_Controller_Action_Helper_');
    }

    protected function _initResourceLoader() {       
        
        $this->_resourceLoader->addResourceType('factory', 'models/factories', 'Factory');
        $this->_resourceLoader->addResourceType('mapper', 'models/mappers', 'Mapper');

//        echo "<pre>".print_r($this->_resourceLoader,true)."</pre>";       
    }

}

