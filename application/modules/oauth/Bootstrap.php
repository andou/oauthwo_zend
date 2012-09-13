<?php
/**
 * 
 * Bootstrap.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
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

/**
 * Defines the code parameter name 
 */
define('CODE', 'code');

/**
 * Defines the grant type parameter name 
 */
define('GRANT_TYPE', 'grant_type');

/**
 * Defines auth code grant type value
 */
define('GRANT_TYPE_AUTHORIZATION_CODE', 'authorization_code');

/**
 * Defines password grant type value
 */
define('GRANT_TYPE_PASSWORD', 'password');

/**
 * Defines client credential grant type value
 */
define('GRANT_TYPE_CLIENT_CREDENTIAL', 'client_credentials');

/**
 * Defines refresh token grant type value
 */
define('GRANT_TYPE_REFRESH_TOKEN', 'refresh_token');

/**
 * Defines the refresh token parameter name 
 */
define('REFRESH_TOKEN', 'refresh_token');

/**
 * Defines the username parameter name 
 */
define('USERNAME', 'username');

/**
 * Defines the password parameter name 
 */
define('PASSWORD', 'password');

/**
 * Defines the web client type value
 */
define('CLIENT_TYPE_WEB', 'web');

/**
 * Defines the user agent client type value
 */
define('CLIENT_TYPE_USER_AGENT', 'user-agent');

/**
 * Defines the native client type value
 */
define('CLIENT_TYPE_NATIVE', 'native');




//to be put in some config file
define('PRIVATE_SIGN_KEY_LOCATION', '/var/www/oauthwo_zend/docs/key.pem');

define('ACCESS_TOKEN_VALIDITY', 600000); //in seconds

define('AUTHORIZATION_CODE_VALIDITY', 60000); //in seconds

define('REFRESH_TOKEN_VALIDITY', 6000000);

/**
 * Realizes the module bootstrap extending Zend_Application_Module_Boostrap
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com> 
 */
class Oauth_Bootstrap extends Zend_Application_Module_Bootstrap {

    /**
     * Modify the standard routing to enable a fixed URL prefix indicating
     * the OAuth framework version(V2) and a separated authorization/token 
     * endpoints 
     */
    public function _initOauthRouting() {
        $ctrl = Zend_Controller_Front::getInstance();
        $router = $ctrl->getRouter();

        $route = new Zend_Controller_Router_Route('v2/oauth/:controller/:action',
                        array('module' => 'oauth',
                            'controller' => 'authorize',
                            'action' => 'index'
                        )
        );

        $router->addRoute('Oauth_module_route', $route);
    }

    /**
     * Adds a path to the helper broker in order to serve our module specific 
     * helpers
     * 
     */
    protected function _initHelperPath() {
        Zend_Controller_Action_HelperBroker::addPath(
                APPLICATION_PATH . '/modules/oauth/controllers/helpers', 'Oauth_Controller_Action_Helper_');
    }

    /**
     * Adds factories and  mappers as resource type, enabling it auto-loading
     */
    protected function _initResourceLoader() {
        $this->_resourceLoader->addResourceType('factory', 'models/factories', 'Factory');
        $this->_resourceLoader->addResourceType('mapper', 'models/mappers', 'Mapper');
    }

}

