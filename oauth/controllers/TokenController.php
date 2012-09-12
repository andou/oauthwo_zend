<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthorizeController
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

    public function init() {

        $this->_helper->viewRenderer->setNoRender(true);

        //inject the factory dependencies
        $factoryLocator = $this->_helper->FactoryLocator;
        $this->_code_factory = $factoryLocator->getAuthorizationCodeFactory();
        $this->_token_factory = $factoryLocator->getTokenFactory();
        $this->_refresh_token_factory = $factoryLocator->getRefreshTokenFactory();
    }

    public function indexAction() {

        $response = "";

        try {
            $this->validateRequest();
        } catch (Oauthwo_RequestException $exc) {

            $error = $exc->getMessage();
            $error_description = $exc->getErrorDescription();

            $response = array(
                'error' => $error,
            );

            if ($error_description)
                $response['error_description'] = $error_description;


            $this->getResponse()->setHttpResponseCode($exc->getHttpCode());
            $this->getResponse()->setBody(json_encode($response));

            $this->getResponse()->setHeader('Content-Type', 'application/json;charset=UTF-8');
            return;
        }


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

    private function compose_response(Oauth_Model_Token $access_token, $refresh_token) {

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
        $request = $this->getRequest();

        if (!$request->isPost())
            throw new Oauthwo_RequestException('invalid_request', 401, 'the request should be a post request');

        $this->validateRequestRFC();
        $this->validateRequestServerSpecific();
    }

    /**
     * This validates the request against the RFC definition.
     * Does not check in the DB, only see presence/absence of values
     * 
     * @throws Exception 
     */
    protected function validateRequestRFC() {
        if (!$grant_type = $this->getRequest()->getParam(GRANT_TYPE))
            throw new Oauthwo_RequestException('invalid_request', 401, 'No grant_type parameter specified');

        switch ($grant_type) {
            case GRANT_TYPE_AUTHORIZATION_CODE:
                if (!$this->getRequest()->getParam(REDIRECT_URI))
                    throw new Oauthwo_RequestException('invalid_request', 401, 'No redirect_uri parameter specified');
                if (!$this->getRequest()->getParam(CODE))
                    throw new Oauthwo_RequestException('invalid_request', 401, 'No code parameter specified');
                break;
            case GRANT_TYPE_CLIENT_CREDENTIAL:
                break;
            case GRANT_TYPE_PASSWORD:
                if (!$this->getRequest()->getParam(USERNAME))
                    throw new Oauthwo_RequestException('invalid_request', 401, 'No username parameter specified');
                if (!$this->getRequest()->getParam(PASSWORD))
                    throw new Oauthwo_RequestException('invalid_request', 401, 'No password parameter specified');
                break;
            case GRANT_TYPE_REFRESH_TOKEN:
                if (!$this->getRequest()->getParam(REFRESH_TOKEN))
                    throw new Oauthwo_RequestException('invalid_request', 401, 'No refresh token parameter specified');
                break;
            default:
                throw new Oauthwo_RequestException('invalid_request', 401, 'unsupported grant type');
                break;
        }
    }

    /**
     * This validates a request with server specific informations
     *
     * @throws Exception 
     */
    protected function validateRequestServerSpecific() {

        $request = $this->getRequest();

        if (!$grant_type = $request->getParam(GRANT_TYPE))
            throw new Oauthwo_RequestException('invalid_request', 401, 'No grant_type parameter specified');

        switch ($grant_type) {
            case GRANT_TYPE_AUTHORIZATION_CODE:
                $this->validateAuthorizationCode($request);
                break;

            case GRANT_TYPE_PASSWORD:
                $this->validatePassword($request);
                break;

            case GRANT_TYPE_CLIENT_CREDENTIAL:
                $this->validateClientCredential($request);
                break;
            case GRANT_TYPE_REFRESH_TOKEN:
                $this->validateRefreshToken($request);
                break;
            default:
                throw new Oauthwo_RequestException('invalid_request', 401, 'unsupported grant type');
                break;
        }
    }

    private function validateRefreshToken($request) {

        $grant_type = $request->getParam(GRANT_TYPE);


        if (!$code = $this->getRequest()->getParam(REFRESH_TOKEN))
            throw new Oauthwo_RequestException('invalid_request', 401, 'No refresh token parameter specified');

        $authorization = $request->getHeader('Authorization');

        $basic = substr($authorization, 0, 5);
        $client_auth = base64_decode(substr($authorization, 6));
        $client_auth = explode(":", $client_auth);

        $client_id = isset($client_auth[0]) ? $client_auth[0] : FALSE;
        $client_secret = isset($client_auth[1]) ? $client_auth[1] : FALSE;

        if ($basic != "Basic" || !$client_id || !$client_secret)
            throw new Oauthwo_RequestException('invalid_request', 401, 'No authorization header');

        if (!$req_client = $this->_helper->ModelLoader->loadClient($client_id))
            throw new Oauthwo_RequestException('invalid_request', 401, 'wrong credential');

        if (!($req_client->getSecret() === $client_secret))
            throw new Oauthwo_RequestException('invalid_request', 401, 'wrong credential');

        if (!$req_client->isAuthorized($grant_type))
            throw new Oauthwo_RequestException('unhautorized_client', 401, 'Client not authorized');


        if ((!$refresh_token = $this->_refresh_token_factory->retrieve($code)) ||
                (!$refresh_token->checkClient($req_client)))
            throw new Oauthwo_RequestException('invalid_request', 401, 'refresh token mismatch or inexistent');

        if (!$refresh_token->checkTimeValidity(time()))
            throw new Oauthwo_RequestException('invalid_request', 401, 'refresh toke expired');
    }

    private function validateAuthorizationCode($request) {

        $grant_type = $request->getParam(GRANT_TYPE);

        if (!$redirect_uri = $request->getParam(REDIRECT_URI))
            throw new Oauthwo_RequestException('invalid_request', 401, 'No redirect_uri parameter specified');
        if (!$code = $request->getParam(CODE))
            throw new Oauthwo_RequestException('invalid_request', 401, 'No code parameter specified');

        $authorization = $request->getHeader('Authorization');

        $basic = substr($authorization, 0, 5);
        $client_auth = base64_decode(substr($authorization, 6));
        $client_auth = explode(":", $client_auth);

        $client_id = isset($client_auth[0]) ? $client_auth[0] : FALSE;
        $client_secret = isset($client_auth[1]) ? $client_auth[1] : FALSE;

        if ($basic != "Basic" || !$client_id || !$client_secret)
            throw new Oauthwo_RequestException('invalid_request', 401, 'No authorization header');

        if (!$req_client = $this->_helper->ModelLoader->loadClient($client_id))
            throw new Oauthwo_RequestException('invalid_request', 401, 'wrong credential');

        if (!($req_client->getSecret() === $client_secret))
            throw new Oauthwo_RequestException('invalid_request', 401, 'wrong credential');

        if (!$req_client->isAuthorized($grant_type))
            throw new Oauthwo_RequestException('unhautorized_client', 401, 'Client not authorized');

        if (!$req_client->checkRedirectUri($redirect_uri))
            throw new Oauthwo_RequestException('invalid_request', 401, 'redirect uri mismatch');

        if ((!$authorization_code = $this->_code_factory->retrieve($code)) ||
                (!$authorization_code->checkClient($req_client)))
            throw new Oauthwo_RequestException('invalid_request', 401, 'authorization code mismatch or inexistent');

        if (!$authorization_code->checkTimeValidity(time()))
            throw new Oauthwo_RequestException('invalid_request', 401, 'authorization code expired');
    }

    private function validatePassword($request) {
        $grant_type = $request->getParam(GRANT_TYPE);


        $authorization = $request->getHeader('Authorization');

        $basic = substr($authorization, 0, 5);
        $client_auth = base64_decode(substr($authorization, 6));
        $client_auth = explode(":", $client_auth);

        $client_id = isset($client_auth[0]) ? $client_auth[0] : FALSE;
        $client_secret = isset($client_auth[1]) ? $client_auth[1] : FALSE;

        if ($basic != "Basic" || !$client_id || !$client_secret)
            throw new Oauthwo_RequestException('invalid_request', 401, 'No authorization header');

        if (!$req_client = $this->_helper->ModelLoader->loadClient($client_id))
            throw new Oauthwo_RequestException('invalid_request', 401, 'wrong credential');

        if (!($req_client->getSecret() === $client_secret))
            throw new Oauthwo_RequestException('invalid_request', 401, 'wrong credential');

        if (!$req_client->isAuthorized($grant_type))
            throw new Oauthwo_RequestException('unhautorized_client', 401, 'Client not authorized');

        if (!$username = $this->getRequest()->getParam(USERNAME))
            throw new Oauthwo_RequestException('invalid_request', 401, 'No username parameter specified');

        if (!$password = $this->getRequest()->getParam(PASSWORD))
            throw new Oauthwo_RequestException('invalid_request', 401, 'No password parameter specified');

        if (!$scope = $this->getRequest()->getParam(SCOPE))
            throw new Oauthwo_RequestException('invalid_request', 401, 'No scopes');
        foreach (explode(" ", trim($scope)) as $s) {
            if (!$this->_helper->ModelLoader->loadScope($s))
                throw new Oauthwo_RequestException('invalid_request', 401, 'invalid scope');
        }

        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $authAdapter->setTableName('user')
                ->setIdentityColumn('user_id')
                ->setCredentialColumn('user_password')
                ->setCredentialTreatment('MD5(?)');

        $authAdapter->setIdentity($username);
        $authAdapter->setCredential($password);

        if ($authAdapter->authenticate()->getCode() != Zend_Auth_Result::SUCCESS)
            throw new Oauthwo_RequestException('invalid_request', 401, 'wrong username or password');
    }

    private function validateClientCredential($request) {
        $grant_type = $request->getParam(GRANT_TYPE);


        $authorization = $request->getHeader('Authorization');

        $basic = substr($authorization, 0, 5);
        $client_auth = base64_decode(substr($authorization, 6));
        $client_auth = explode(":", $client_auth);

        $client_id = isset($client_auth[0]) ? $client_auth[0] : FALSE;
        $client_secret = isset($client_auth[1]) ? $client_auth[1] : FALSE;

        if ($basic != "Basic" || !$client_id || !$client_secret)
            throw new Oauthwo_RequestException('invalid_request', 401, 'No authorization header');

        if (!$req_client = $this->_helper->ModelLoader->loadClient($client_id))
            throw new Oauthwo_RequestException('invalid_request', 401, 'wrong credential');

        if (!($req_client->getSecret() === $client_secret))
            throw new Oauthwo_RequestException('invalid_request', 401, 'wrong credential');

        if (!$req_client->isAuthorized($grant_type))
            throw new Oauthwo_RequestException('unhautorized_client', 401, 'Client not authorized');

        if (!$scope = $this->getRequest()->getParam(SCOPE))
            throw new Oauthwo_RequestException('invalid_request', 401, 'No scopes');
        foreach (explode(" ", trim($scope)) as $s) {
            if (!$this->_helper->ModelLoader->loadScope($s))
                throw new Oauthwo_RequestException('invalid_request', 401, 'invalid scope');
        }
    }

}

?>
