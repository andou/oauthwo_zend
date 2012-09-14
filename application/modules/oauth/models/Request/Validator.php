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
    const MISSING_RESPONSE_TYPE = 'missing_response_type';
    const MISSING_CLIENT = 'missing_client';
    const MISSING_REDIRECT_URI = 'missing_redirect_uri';
    const MISSING_MISSING_SCOPE = 'missing_scope';

    protected $_messageTemplates = array(
        self::WRONG_INSTANCE => "validating obj does not implements Oauth_Request_Interface",
        self::WRONG_ENDPOINT => "Endpoint not valid",
        self::WRONG_RESPONSE_TYPE => "Response type not supported",
        self::WRONG_SCOPE => "Specified scope does not exists",
        self::MISSING_RESPONSE_TYPE => "Response type not specified",
        self::MISSING_CLIENT => "client id not specified",
        self::MISSING_REDIRECT_URI => "redirect uri not specified",
        self::MISSING_MISSING_SCOPE => "scope not specified",        
        self::WRONG_CLIENT_ID => "Client does not exists",
        self::WRONG_CLIENT_TYPE => "Client is not authorized to do that!",
        self::WRONG_REDIRECT_URI => "Redirect uri is wrong!",
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
        
        if (!$redirect_uri = $request->getRedirectUri()){
            $this->_error(self::MISSING_REDIRECT_URI);
            return FALSE;
        }
               
        if (!$scope = $request->getScope()){
            $this->_error(self::MISSING_MISSING_SCOPE);
            return FALSE;
        }        
        
        //////////////////////////////////////
        ///////DATABASE AWARE CHECKS//////////
        //////////////////////////////////////
        
        /////SERVER SPECIFIC CHECKS
        
        //Check our scopes
        foreach (explode(" ", trim($scope)) as $s) {
            $scopeMapper = new Oauth_Mapper_Scope();
            if(!$scopeMapper->find($s)){
                $this->_error(self::WRONG_SCOPE);
                return FALSE;
            }
        }
        
        //Check client
        $clientMapper = new Oauth_Mapper_Client();
        
        if (!$requesting_client = $clientMapper->find($client_id)){
            $this->_error(self::WRONG_CLIENT_ID);
            return FALSE;
        }
        
        if (!$requesting_client->isAuthorized($reponseType)){
            $this->_error(self::WRONG_CLIENT_TYPE);
            return FALSE;
        }

        if (!$requesting_client->checkRedirectUri($redirect_uri)){
            $this->_error(self::WRONG_REDIRECT_URI);
            return FALSE;
        }

        return TRUE;
    }

    private function isValidTokenEndpointRequest(Oauth_Request_Interface $request) {
        return TRUE;
    }

}
