<?php

/**
 * 
 * Interface.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 * This interface models a Basic Request Model object in the OAuth 2.0 
 * environment. It is possible to add new parameters.
 * 
 */
interface Oauth_Request_Interface {

    /**
     * Returns the endpoint of this request
     * 
     */
    function getEndpoint();

    /**
     * If present in this request, returns the Authorization header/scheme
     * 
     * @return mixed Authorization header/scheme if present in this request, FALSE otherwise 
     */
    function getAuthorization();

    /**
     * If present in this request, returns the parameter: response_type
     * 
     * @return mixed response_type parameter if present in this request, FALSE otherwise 
     */
    function getResponseType();

    /**
     * If present in this request, returns the parameter: client_id
     * 
     * @return mixed client_id parameter if present in this request, FALSE otherwise 
     */
    function getClientId();

    /**
     * If present in this request, returns the parameter: redirect_uri
     * 
     * @return mixed redirect_uri parameter if present in this request, FALSE otherwise 
     */
    function getRedirectUri();

    /**
     * If present in this request, returns the parameter: scope
     * 
     * @return mixed scope parameter if present in this request, FALSE otherwise 
     */
    function getScope();

    /**
     * If present in this request, returns the parameter: state
     * 
     * @return mixed state parameter if present in this request, FALSE otherwise 
     */
    function getState();

    /**
     * If present in this request, returns the parameter: code
     * 
     * @return mixed code parameter if present in this request, FALSE otherwise 
     */
    function getCode();

    /**
     * If present in this request, returns the parameter: grant_type
     * 
     * @return mixed grant_type parameter if present in this request, FALSE otherwise 
     */
    function getGrantType();

    /**
     * If present in this request, returns the parameter: refresh_token
     * 
     * @return mixed refresh_token parameter if present in this request, FALSE otherwise 
     */
    function getRefreshToken();

    /**
     * If present in this request, returns the parameter: username
     * 
     * @return mixed username parameter if present in this request, FALSE otherwise 
     */
    function getUsername();

    /**
     * If present in this request, returns the parameter: password
     * 
     * @return mixed password parameter if present in this request, FALSE otherwise 
     */
    function getPassword();
}
