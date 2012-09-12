<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RedirectUriFormatter
 *
 * @author andou
 */
class Oauth_Controller_Action_Helper_RedirectUriFormatter extends Zend_Controller_Action_Helper_Abstract {

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

    public function authorizationCodeRedirect($redirect_uri, Oauth_Model_AuthorizationCode $code, $state) {

        $state = $state ? "&state=" . $state : "";

        $code = '?code=' . $code->getCode();

        $url = $redirect_uri . $code . $state;

        return $url;
    }

    public function errorRedirect($redirect_uri, $state = null) {

        $state = $state ? "&state=" . $state : "";

        $error = '?error=access_denied';

        $url = $redirect_uri . $error . $state;

        return $url;
    }

}

?>
