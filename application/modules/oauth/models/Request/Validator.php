<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Validator
 *
 * @author antonio.pastorino@gmail.com
 */
class Oauth_Request_Validator extends Zend_Validate_Abstract {

    const WRONG_INSTANCE = 'wrong_instance';
    const WRONG_ENDPOINT = 'wrong_endpoint';
    const WRONG_RESPONSE_TYPE = 'wrong_response_type';
    const WRONG_SCOPE = 'wrong_scope';
    const WRONG_REDIRECT_URI = 'wrong_redirect_uri';
    const WRONG_CLIENT_ID = 'wrong_client_id';
    const WRONG_CLIENT_TYPE = 'wrong_client_type';
    const WRONG_GRANT_TYPE = 'wrong_grant_type';
    const WRONG_CLIENT_CREDENTIAL = 'wrong_client_credentials';
    const WRONG_USER_PW = 'wrong_username_or_password';
    const WRONG_AUTHORIZATION_CODE = 'wrong_authorization_code';
    const WRONG_REFRESH_TOKEN = 'wrong_refresh_token';
    const MISSING_RESPONSE_TYPE = 'missing_response_type';
    const MISSING_CLIENT = 'missing_client';
    const MISSING_REDIRECT_URI = 'missing_redirect_uri';
    const MISSING_MISSING_SCOPE = 'missing_scope';
    const MISSING_GRANT_TYPE = 'missing_grant_type';
    const MISSING_CODE = 'missing_code';
    const MISSING_USERNAME = 'missing_username';
    const MISSING_PASSWORD = 'missing_password';
    const MISSING_REFRESH_TOKEN = 'missing_refresh_token';
    const MISSING_AUTHORIZATION_HEADER = 'missing_authorization_header';
    const NOT_POST_REQUEST = 'request_not_post';
    const AUTHORIZATION_CODE_EXPIRED = 'expired_authorization_code';
    const REFRESH_TOKEN_EXPIRED = 'expired_refresh_token';

    protected $_messageTemplates = array(
        self::WRONG_INSTANCE => "internal_error:validating obj does not implements Oauth_Request_Interface",
        self::WRONG_ENDPOINT => "invalid_request:Endpoint not valid",
        self::WRONG_RESPONSE_TYPE => "invalid_request:Response type not supported",
        self::WRONG_SCOPE => "invalid_request:Specified scope does not exists",
        self::MISSING_RESPONSE_TYPE => "invalid_request:Response type not specified",
        self::MISSING_CLIENT => "invalid_request:client id not specified",
        self::MISSING_REDIRECT_URI => "invalid_request:redirect uri not specified",
        self::MISSING_MISSING_SCOPE => "invalid_request:scope not specified",
        self::WRONG_CLIENT_ID => "invalid_request:Client does not exists",
        self::WRONG_CLIENT_TYPE => "unauthorized_client:Client is not authorized to do that!",
        self::WRONG_REDIRECT_URI => "invalid_request:Redirect uri is wrong!",
        self::NOT_POST_REQUEST => "invalid_request:the request should be a post request",
        self::WRONG_GRANT_TYPE => "invalid_request:unsupported grant type",
        self::MISSING_GRANT_TYPE => "invalid_request:grant type not specified",
        self::MISSING_CODE => "invalid_request:authorization code not specified",
        self::MISSING_USERNAME => "invalid_request:username not specified",
        self::MISSING_PASSWORD => "invalid_request:password not specified",
        self::MISSING_REFRESH_TOKEN => "invalid_request:refresh token not specified",
        self::MISSING_AUTHORIZATION_HEADER => "invalid_request:authorization header not specified",
        self::WRONG_CLIENT_CREDENTIAL => "invalid_request:wrong client credentials",
        self::WRONG_USER_PW => "invalid_request:wrong username or password",
        self::WRONG_AUTHORIZATION_CODE => "invalid_request:wrong auth code",
        self::AUTHORIZATION_CODE_EXPIRED => "invalid_request:expired auth code",
        self::WRONG_REFRESH_TOKEN => "invalid_request:wrong refresh token",
        self::REFRESH_TOKEN_EXPIRED => "invalid_request:expired refresh token",
    );

    public function isValid($value) {
        $this->_setValue($value);

        //first of all, let's check the instance of the object to be validated
        if (!$value instanceof Oauth_Request_Interface) {
            $this->_error(self::WRONG_INSTANCE);
            return FALSE;
        }

        //now, let's see which endpoint we should check and call the correct method
        switch ($value->getEndpoint()) {
            case 'authorize':
                return $this->isValidAuthorizeEndpointRequest($value);
            case 'token':
                return $this->isValidTokenEndpointRequest($value);
            default:
                $this->_error(self::WRONG_ENDPOINT);
                return FALSE;
        }
    }

    private function isValidAuthorizeEndpointRequest(Oauth_Request_Interface $request) {

        
        
        $reponseType = $request->getResponseType();

        //////////////////////////////////////
        ///////DATABASE AGNOSTIC CHECKS///////
        //////////////////////////////////////        
        /////GENERIC CHECKS
        //search for the response type parameter. If not present, ERROR!!!
        if (!$reponseType) {
            $this->_error(self::MISSING_RESPONSE_TYPE);
            return FALSE;
        }

        //check for the response type and for the client parameters
        switch ($reponseType) {
            case RESPONSE_TYPE_CODE:
            case RESPONSE_TYPE_TOKEN:
                if (!$client_id = $request->getClientId()) {
                    $this->_error(self::MISSING_CLIENT);
                    return FALSE;
                }
                break;
            default:
                $this->_error(self::WRONG_RESPONSE_TYPE);
                return FALSE;
        }

        /////SERVER SPECIFIC CHECKS

        
        if (!$redirect_uri = $request->getRedirectUri()) {
            $this->_error(self::MISSING_REDIRECT_URI);
            return FALSE;
        }
        
        
        //////////////////////////////////////
        ///////DATABASE AWARE CHECKS//////////
        //////////////////////////////////////
        /////SERVER SPECIFIC CHECKS
        //Check our scopes

        if (!$this->validateScopes($request)) {
            return FALSE;
        }
        



        //Check client
        $clientMapper = new Oauth_Mapper_Client();

        
        if (!$requesting_client = $clientMapper->find($client_id)) {
            $this->_error(self::WRONG_CLIENT_ID);
            return FALSE;
        }

        if (!$requesting_client->isAuthorized($reponseType)) {
            $this->_error(self::WRONG_CLIENT_TYPE);
            return FALSE;
        }

        if (!$requesting_client->checkRedirectUri($redirect_uri)) {
            $this->_error(self::WRONG_REDIRECT_URI);
            return FALSE;
        }

        return TRUE;
    }

    private function isValidTokenEndpointRequest(Oauth_Request_Interface $request) {

        if (!$request->isPost()) {
            $this->_error(self::NOT_POST_REQUEST);
            return FALSE;
        }


        if (!$grant_type = $request->getGrantType()) {
            $this->_error(self::MISSING_GRANT_TYPE);
            return FALSE;
        }
        switch ($grant_type) {
            case GRANT_TYPE_AUTHORIZATION_CODE:
                if (!$request->getRedirectUri()) {
                    $this->_error(self::MISSING_REDIRECT_URI);
                    return FALSE;
                }
                if (!$request->getCode()) {
                    $this->_error(self::MISSING_CODE);
                    return FALSE;
                }
                
                return $this->validateAuthorizationCode($request);
            case GRANT_TYPE_PASSWORD:
                if (!$request->getUsername()) {
                    $this->_error(self::MISSING_USERNAME);
                    return FALSE;
                }
                if (!$request->getPassword()) {
                    $this->_error(self::MISSING_PASSWORD);
                    return FALSE;
                }
                
                return $this->validatePassword($request);
            case GRANT_TYPE_REFRESH_TOKEN:
                if (!$request->getRefreshToken()) {
                    $this->_error(self::MISSING_REFRESH_TOKEN);
                    return FALSE;
                }
                
                return $this->validateRefreshToken($request);
            case GRANT_TYPE_CLIENT_CREDENTIAL:
                return $this->validateClientCredential($request);
            default:
                $this->_error(self::WRONG_GRANT_TYPE);
                return FALSE;
                break;
        }
        return TRUE;
    }

    private function validateClientCredential(Oauth_Request_Interface $request) {

        //check client
        if (!$this->validateAuthorize($request)) {
            return FALSE;
        }

        //checks scopes
        if (!$this->validateScopes($request)) {
            return FALSE;
        }
        
        return TRUE;
    }

    private function validateAuthorize(Oauth_Request_Interface $request) {
        //creating the mappers - should be removed
        $clientMapper = new Oauth_Mapper_Client();


        $authorization = $request->getAuthorization();
        $basic = substr($authorization, 0, 5);
        $client_auth = base64_decode(substr($authorization, 6));
        $client_auth = explode(":", $client_auth);
        $client_id = isset($client_auth[0]) ? $client_auth[0] : FALSE;
        $client_secret = isset($client_auth[1]) ? $client_auth[1] : FALSE;

        if ($basic != "Basic" || !$client_id || !$client_secret) {
            $this->_error(self::MISSING_AUTHORIZATION_HEADER);
            return FALSE;
        }

        if (!$client = $clientMapper->find($client_id)) {
            $this->_error(self::WRONG_CLIENT_CREDENTIAL);
            return FALSE;
        }


        //validate client        
        if (!($client->getSecret() === $client_secret)) {
            $this->_error(self::WRONG_CLIENT_CREDENTIAL);
            return FALSE;
        }


        $grant_type = $request->getGrantType();

        if (!$client->isAuthorized($grant_type)) {
            $this->_error(self::WRONG_CLIENT_TYPE);
            return FALSE;
        }

        return $client;
    }

    private function validateScopes(Oauth_Request_Interface $request) {

        $scopeMapper = new Oauth_Mapper_Scope();

        
        if (!$scope = $request->getScope()) {
            $this->_error(self::MISSING_MISSING_SCOPE);
            return FALSE;
        }

        $scopes = explode(" ", trim($scope));       
        
        foreach ($scopes as $s) {
            if (!$scopeMapper->find($s)) {                
                $this->_error(self::WRONG_SCOPE);
                return FALSE;
            }
        }
        
        return TRUE;
    }

    private function validatePassword(Oauth_Request_Interface $request) {

        if (!$this->validateAuthorize($request)) {
            return FALSE;
        }

        if (!$this->validateScopes($request)) {
            return FALSE;
        }

        if (!$username = $request->getUsername()) {
            $this->_error(self::MISSING_USERNAME);
            return FALSE;
        }

        if (!$password = $request->getPassword()) {
            $this->_error(self::MISSING_PASSWORD);
            return FALSE;
        }

        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $authAdapter->setTableName('user')
                ->setIdentityColumn('user_id')
                ->setCredentialColumn('user_password')
                ->setCredentialTreatment('MD5(?)');

        $authAdapter->setIdentity($username);
        $authAdapter->setCredential($password);

        if ($authAdapter->authenticate()->getCode() != Zend_Auth_Result::SUCCESS) {
            $this->_error(self::WRONG_USER_PW);
            return FALSE;
        }
        
        return TRUE;
    }

    private function validateAuthorizationCode(Oauth_Request_Interface $request) {

        if (!$redirect_uri = $request->getRedirectUri()) {
            $this->_error(self::MISSING_REDIRECT_URI);
            return FALSE;
        }

        if (!$code = $request->getCode()) {
            $this->_error(self::MISSING_CODE);
            return FALSE;
        }

        if (!$req_client = $this->validateAuthorize($request)) {
            return FALSE;
        }

        if (!$req_client->checkRedirectUri($redirect_uri)) {
            $this->_error(self::WRONG_REDIRECT_URI);
            return FALSE;
        }

        $code_builder = new Oauth_Builder_AuthorizationCode();


        if ((!$authorization_code = $code_builder->retrieve($code)) ||
                (!$authorization_code->checkClient($req_client))) {
            $this->_error(self::WRONG_AUTHORIZATION_CODE);
            return FALSE;
        }

        if (!$authorization_code->checkTimeValidity(time())) {
            $this->_error(self::AUTHORIZATION_CODE_EXPIRED);
            return FALSE;
        }
        
        return TRUE;
    }

    private function validateRefreshToken(Oauth_Request_Interface $request) {

        if (!$code = $request->getRefreshToken()) {
            $this->_error(self::MISSING_REFRESH_TOKEN);
            return FALSE;
        }

        if (!$req_client = $this->validateAuthorize($request)) {
            return FALSE;
        }

        $code_builder = new Oauth_Builder_RefreshToken();

        if ((!$refresh_token = $code_builder->retrieve($code)) ||
                (!$refresh_token->checkClient($req_client))) {
            $this->_error(self::WRONG_REFRESH_TOKEN);
            return FALSE;
        }

        if (!$refresh_token->checkTimeValidity(time())) {
            $this->_error(self::REFRESH_TOKEN_EXPIRED);
            return FALSE;
        }
        
        return TRUE;
    }

}
