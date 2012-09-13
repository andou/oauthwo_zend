<?php

/**
 * 
 * RedirectUriFormatter.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Extends an abstract helper and is used by controllers to build redirect URIs
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 *  
 */
class Oauth_Controller_Action_Helper_RedirectUriFormatter extends Zend_Controller_Action_Helper_Abstract {

    /**
     * Builds a redirect uri to send a token, which contains a fragment
     *
     * @param string $redirect_uri
     * @param Oauth_Model_Token $token
     * @param string $state
     * @return string the requested uri formatter
     */
    public function tokenRedirect($redirect_uri, Oauth_Model_Token $token, $state) {

        $state = $state ?
                "&state=" . $state :
                "";
        $expire = $token->getExpireDate() ?
                "&expires_in=" . $token->getExpireDate() :
                "";

        $token_type = "&token_type="
                . $token->getType();


        $url = $redirect_uri . '#access_token='
                . $token->getCode()
                . $state
                . $token_type
                . $expire;

        return $url;
    }

    /**
     * Builds a redirect URI to deliver an authorization code
     *
     * @param string $redirect_uri
     * @param Oauth_Model_AuthorizationCode $code
     * @param string $state
     * @return string 
     */
    public function authorizationCodeRedirect($redirect_uri, Oauth_Model_AuthorizationCode $code, $state) {

        $state = $state ? "&state=" . $state : "";

        $code = '?code=' . $code->getCode();

        $url = $redirect_uri . $code . $state;

        return $url;
    }

    /**
     * Builds a redirect URI to deliver an error message
     *
     * @param string $redirect_uri
     * @param string $state
     * @return string 
     */
    public function errorRedirect($redirect_uri, $state = null) {

        $state = $state ? "&state=" . $state : "";

        $error = '?error=access_denied';

        $url = $redirect_uri . $error . $state;

        return $url;
    }

}
