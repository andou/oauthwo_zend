<?php

/**
 * 
 * Request.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 * Wrapper class of a Zend Request
 *
 * @author antonio.pastorino@gmail.com
 */
class Oauth_Model_Request implements Oauth_Request_Interface {

    /**
     * The request object to wrap
     *
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * Construct a Request injecting the Zend request
     *
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function __construct(Zend_Controller_Request_Abstract $request) {
        $this->_request = $request;
    }

    /**
     * Helper function to retrieve params from the request
     *
     * @param string $param_name
     * @return mixed
     */
    private function getParam($param_name) {
        return $this->_request->getParam($param_name, FALSE);
    }

    /**
     * Returns the endpoint of this request
     * 
     */
    function getEndpoint() {
        return $this->_request->getControllerName();
    }

    /**
     * If present in this request, returns the Authorization header/scheme
     * 
     * @return mixed Authorization header/scheme if present in this request, FALSE otherwise 
     */
    function getAuthorization() {
        return $this->_request->getHeader('Authorization') ?
                $this->_request->getHeader('Authorization') :
                FALSE;
    }

    /**
     * If present in this request, returns the parameter: response_type
     * 
     * @return mixed response_type parameter if present in this request, FALSE otherwise 
     */
    function getResponseType() {
        return $this->getParam(RESPONSE_TYPE);
    }

    /**
     * If present in this request, returns the parameter: client_id
     * 
     * @return mixed client_id parameter if present in this request, FALSE otherwise 
     */
    function getClientId() {
        return $this->getParam(CLIENT_ID);
    }

    /**
     * If present in this request, returns the parameter: redirect_uri
     * 
     * @return mixed redirect_uri parameter if present in this request, FALSE otherwise 
     */
    function getRedirectUri() {
        return $this->getParam(REDIRECT_URI);
    }

    /**
     * If present in this request, returns the parameter: scope
     * 
     * @return mixed scope parameter if present in this request, FALSE otherwise 
     */
    function getScope() {
        return $this->getParam(SCOPE);
    }

    /**
     * If present in this request, returns the parameter: state
     * 
     * @return mixed state parameter if present in this request, FALSE otherwise 
     */
    function getState() {
        return $this->getParam(STATE);
    }

    /**
     * If present in this request, returns the parameter: code
     * 
     * @return mixed code parameter if present in this request, FALSE otherwise 
     */
    function getCode() {
        return $this->getParam(CODE);
    }

    /**
     * If present in this request, returns the parameter: grant_type
     * 
     * @return mixed grant_type parameter if present in this request, FALSE otherwise 
     */
    function getGrantType() {
        return $this->getParam(GRANT_TYPE);        
    }

    /**
     * If present in this request, returns the parameter: refresh_token
     * 
     * @return mixed refresh_token parameter if present in this request, FALSE otherwise 
     */
    function getRefreshToken() {
        return $this->getParam(REFRESH_TOKEN);        
    }

    /**
     * If present in this request, returns the parameter: username
     * 
     * @return mixed username parameter if present in this request, FALSE otherwise 
     */
    function getUsername() {
        return $this->getParam(USERNAME);        
    }

    /**
     * If present in this request, returns the parameter: password
     * 
     * @return mixed password parameter if present in this request, FALSE otherwise 
     */
    function getPassword() {
        return $this->getParam(PASSWORD);        
    }

}
