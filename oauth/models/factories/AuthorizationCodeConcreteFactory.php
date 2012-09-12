<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TokenConcreteFactory
 *
 * @author andou
 */
class Oauth_Factory_AuthorizationCodeConcreteFactory{

    /**
     * Creates an authorization code
     *
     * @return Oauth_Model_IAuthorizationCode
     */
    public function create(Oauth_Model_Client $client, $scopes, Oauth_Model_ResourceOwner $resource_owner) {
        
        $code = $this->generateRandomNumber(20);


        $authorization_code = new Oauth_Model_AuthorizationCode();

        $authorization_code->setCode($code);
        $authorization_code->setClient($client);
        $authorization_code->setScopes($scopes);
        $authorization_code->setResourceOwnerId($resource_owner->getId());

        //Saving to the DB - should we?
        $authorizationCodeMapper = new Oauth_Mapper_AuthorizationCode();
        $authorizationCodeMapper->save($authorization_code);

        return $authorization_code;
    }

    public function retrieve($code) {
        $codeMapper = new Oauth_Mapper_AuthorizationCode();
        return $codeMapper->find($code);
    }
    
    public function consume($code){        
        $codeMapper = new Oauth_Mapper_AuthorizationCode();
        
        $authorization_code = $codeMapper->find($code);
        
        $codeMapper->delete($code);
        
        return $authorization_code;
    }

    private function generateRandomNumber($codeLen) {
        if (file_exists('/dev/urandom')) { // Get 100 bytes of random data
            $randomData = file_get_contents('/dev/urandom', false, null, 0, 100) . uniqid(mt_rand(), true);
        } else {
            $randomData = mt_rand() . mt_rand() . mt_rand() . mt_rand() . microtime(true) . uniqid(mt_rand(), true);
        }
        return substr(hash('sha512', $randomData), 0, $codeLen);
    }

}

