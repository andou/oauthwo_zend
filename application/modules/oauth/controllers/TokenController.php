<?php

/**
 * 
 * TokenController.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements the Token endpoint of the OAuth 2.0 framework
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
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

        $request = new Oauth_Model_Request($this->getRequest());

        switch ($grant_type) {
            case GRANT_TYPE_AUTHORIZATION_CODE:
                $response = $this->processGrantTypeAuthorizationCode($request);
                break;
            case GRANT_TYPE_REFRESH_TOKEN:
                $response = $this->processGrantTypeRefreshToken($request);
                break;
            case GRANT_TYPE_PASSWORD:
                $response = $this->processGrantTypePassword($request);
                break;
            case GRANT_TYPE_CLIENT_CREDENTIAL:
                $response = $this->processGrantTypeClientCredential($request);
                break;
        }


        $this->getResponse()->setBody($response);
    }

    /**
     * Process a request with authorization code grant type
     *
     * @param Oauth_Model_Request $request
     * @return string the request to be outputted
     */
    public function processGrantTypeAuthorizationCode(Oauth_Model_Request $request) {

        $modelLoader = $this->_helper->ModelLoader;
        //client        
        $client = $this->retrieveClientFromHeader($request);
        //code
        $code_val = $request->getCode();
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
        return $this->compose_response($token, $refresh_token);
    }

    /**
     * Process a request with password grant type
     *
     * @param Oauth_Model_Request $request
     * @return string the request to be outputted
     */
    protected function processGrantTypePassword(Oauth_Model_Request $request) {

        $modelLoader = $this->_helper->ModelLoader;
        //client
        $client = $this->retrieveClientFromHeader($request);
        //resource owner
        $resource_owner_id = $this->getRequest()->getParam(USERNAME);
        $resource_owner = $modelLoader->loadResourceOwner($resource_owner_id);
        //scopes
        $scopes = $request->getScope();
        //token
        $token = $this->_token_factory->create($resource_owner, $scopes);
        //refresh token
        $refresh_token = $this->_refresh_token_factory->create($client, $scopes, $resource_owner);
        //response
        return $this->compose_response($token, $refresh_token);
    }

    /**
     * Process a request with refresh token grant type
     *
     * @param Oauth_Model_Request $request
     * @return string the request to be outputted
     */
    protected function processGrantTypeRefreshToken(Oauth_Model_Request $request) {

        $modelLoader = $this->_helper->ModelLoader;
        $client = $this->retrieveClientFromHeader($request);

        $code_val = $request->getRefreshToken();
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
        return $this->compose_response($token, $refresh_token);
    }

    /**
     * Process a request with client credential grant type
     *
     * @param Oauth_Model_Request $request
     * @return string the request to be outputted
     */
    protected function processGrantTypeClientCredential(Oauth_Model_Request $request) {

        $modelLoader = $this->_helper->ModelLoader;
        
        $client = $this->retrieveClientFromHeader($request);
        //resource owner
        $resource_owner = $modelLoader->loadResourceOwner($client->getId());
        //scopes
        $scopes = $request->getScope();
        //token
        $token = $this->_token_factory->create($resource_owner, $scopes);
        //response
        return $this->compose_response($token, NULL);
    }

    /**
     * Helper function to retrieve a client from the header of a request
     *
     * @param Oauth_Model_Request $request
     * @return Oauth_Model_Client
     */
    private function retrieveClientFromHeader(Oauth_Model_Request $request) {
        $authorization = $request->getAuthorization();
        $client_auth = base64_decode(substr($authorization, 6));
        $client_auth = explode(":", $client_auth);
        $client_id = $client_auth[0];
        return $this->_helper->ModelLoader->loadClient($client_id);
    }

    /**
     * Compose a response
     *
     * @param Oauth_Model_Token $access_token
     * @param Oauth_Model_RefreshToken $refresh_token
     * 
     * @return string the JSON encoded object to be returned
     */
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
     * This method calls the Validator to check if the request is valid or not
     * 
     * @return boolean TRUE if the request is valid, FALSE otherwise
     */
    protected function validateRequest() {
        $request = new Oauth_Model_Request($this->getRequest());

        if (!$this->_request_validator->isValid($request)) {
            $response = Array();

            $messages = $this->_request_validator->getMessages();
            $last_msg = explode(":", array_pop($messages));
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

