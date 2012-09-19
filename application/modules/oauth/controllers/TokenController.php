<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthorizeController Controller
 *
 * @author andou
 */
class Oauth_TokenController extends Zend_Controller_Action {

    /**
     *
     * @var Oauth_Factory_TokenAbstractFactory
     */
    protected $_token_factory;

    /**
     *
     * @var Oauth_Factory_AuthorizationCodeAbstractFactory
     */
    protected $_code_factory;

    /**
     *
     * @var Oauth_Factory_RefreshTokenAbstractFactory
     */
    protected $_refresh_token_factory;

    /**
     * The request validator
     *
     * @var Oauth_Request_Validator
     */
    protected $_request_validator;

    public function init() {

        $this->_request_validator = new Oauth_Request_Validator();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        //inject the factory dependencies
        $factoryLocator = $this->_helper->FactoryLocator;
        $this->_code_factory = $factoryLocator->getAuthorizationCodeFactory();
        $this->_token_factory = $factoryLocator->getTokenFactory();
        $this->_refresh_token_factory = $factoryLocator->getRefreshTokenFactory();
    }

    public function indexAction() {

        $response = "";

        if (!$this->validateRequest())
            return;
        
        $grant_type = $this->getRequest()->getParam(GRANT_TYPE);
        $modelLoader = $this->_helper->ModelLoader;

        switch ($grant_type) {
            case GRANT_TYPE_AUTHORIZATION_CODE:
                //client
                $authorization = $this->getRequest()->getHeader('Authorization');
                $client_auth = base64_decode(substr($authorization, 6));
                $client_auth = explode(":", $client_auth);
                $client_id = $client_auth[0];
                $client = $this->_helper->ModelLoader->loadClient($client_id);
                //code
                $code_val = $this->getRequest()->getParam(CODE);
                $code = $this->_code_factory->consume($code_val);
                //resource owner
                $resource_owner_id = $code->getResourceOwnerId();
                $resource_owner = $modelLoader->loadResourceOwner($resource_owner_id);
                //scopes
                $scopes = $code->getScopes();
                //token
                $token = $this->_token_factory->create($resource_owner, $scopes);
                //refresh token
                $refresh_token = $this->_refresh_token_factory->create($client, $scopes, $resource_owner);
                //response
                $response = $this->compose_response($token, $refresh_token);
                break;

            case GRANT_TYPE_REFRESH_TOKEN:
                //client
                $authorization = $this->getRequest()->getHeader('Authorization');
                $client_auth = base64_decode(substr($authorization, 6));
                $client_auth = explode(":", $client_auth);
                $client_id = $client_auth[0];
                $client = $this->_helper->ModelLoader->loadClient($client_id);
                //code
                $code_val = $this->getRequest()->getParam(REFRESH_TOKEN);
                $code = $this->_refresh_token_factory->consume($code_val);
                //resource owner
                $resource_owner_id = $code->getResourceOwnerId();
                $resource_owner = $modelLoader->loadResourceOwner($resource_owner_id);
                //scopes
                $scopes = $code->getScopes();
                //token
                $token = $this->_token_factory->create($resource_owner, $scopes);
                //refresh token
                $refresh_token = $this->_refresh_token_factory->create($client, $scopes, $resource_owner);
                //response
                $response = $this->compose_response($token, $refresh_token);
                break;
            case GRANT_TYPE_PASSWORD:
                //client
                $authorization = $this->getRequest()->getHeader('Authorization');
                $client_auth = base64_decode(substr($authorization, 6));
                $client_auth = explode(":", $client_auth);
                $client_id = $client_auth[0];
                $client = $this->_helper->ModelLoader->loadClient($client_id);
                //resource owner
                $resource_owner_id = $this->getRequest()->getParam(USERNAME);
                $resource_owner = $modelLoader->loadResourceOwner($resource_owner_id);
                //scopes
                $scopes = $this->getRequest()->getParam(SCOPE);
                //token
                $token = $this->_token_factory->create($resource_owner, $scopes);
                //refresh token
                $refresh_token = $this->_refresh_token_factory->create($client, $scopes, $resource_owner);
                //response
                $response = $this->compose_response($token, $refresh_token);
                break;
            case GRANT_TYPE_CLIENT_CREDENTIAL:
                //client
                $authorization = $this->getRequest()->getHeader('Authorization');
                $client_auth = base64_decode(substr($authorization, 6));
                $client_auth = explode(":", $client_auth);
                $client_id = $client_auth[0];
                //resource owner
                $resource_owner = $modelLoader->loadResourceOwner($client_id);
                //scopes
                $scopes = $this->getRequest()->getParam(SCOPE);
                //token
                $token = $this->_token_factory->create($resource_owner, $scopes);
                //response
                $response = $this->compose_response($token, NULL);
                break;
        }


        $this->getResponse()->setBody($response);
    }

    private function compose_response(Oauth_Model_Token $access_token, Oauth_Model_RefreshToken $refresh_token) {

        $this->getResponse()->setHeader('Pragma', 'no-cache');
        $this->getResponse()->setHeader('Content-Type', 'application/json;charset=UTF-8');
        $this->getResponse()->setHeader('Cache-Control', 'no-store');

        $response_token = array(
            'access_token' => $access_token->getCode(),
            'token_type' => $access_token->getType(),
            'expires_in' => $access_token->getExpireDate(),
        );

        if (isset($refresh_token)) {
            $response_token['refresh_token'] = $refresh_token->getCode();
        }


        return json_encode($response_token);
    }

    /**
     * Index method to validate an obtaining grant request
     * 
     */
    protected function validateRequest() {
        $request = new Oauth_Model_Request($this->getRequest());

        if (!$this->_request_validator->isValid($request)) {
            $response = Array();
            
            $messages = $this->_request_validator->getMessages();
            $last_msg = explode(":",array_pop($messages));
            $response['error'] = $last_msg[0];
            $response['error_description'] = isset($last_msg[1]) ? $last_msg[1] : "";

            $this->getResponse()->setHttpResponseCode(401);
            $this->getResponse()->setBody(json_encode($response));
            $this->getResponse()->setHeader('Content-Type', 'application/json;charset=UTF-8');

            return FALSE;
        }

        return TRUE;
    }

}

