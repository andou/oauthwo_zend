<?php
/**
 * 
 * AuthorizationCode.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Builder class to create Authorization Codes
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Builder_AuthorizationCode{

    /**
     * Creates an authorization code
     * 
     * @param Oauth_Model_Client $client
     * @param string $scopes
     * @param Oauth_Model_ResourceOwner $resource_owner
     * @return Oauth_Model_AuthorizationCode 
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

    /**
     * Retrieves an Authorization Code from the DB
     *
     * @param string $code
     * @return Oauth_Model_AuthorizationCode
     */
    public function retrieve($code) {
        $codeMapper = new Oauth_Mapper_AuthorizationCode();
        return $codeMapper->find($code);
    }
    
    /**
     * Consumes an Authorization Code deleting it from the DB
     *
     * @param string $code
     * @return Oauth_Model_AuthorizationCode 
     */
    public function consume($code){        
        $codeMapper = new Oauth_Mapper_AuthorizationCode();
        
        $authorization_code = $codeMapper->find($code);
        
        $codeMapper->delete($code);
        
        return $authorization_code;
    }

    /**
     * Generates a $codeLen chars pseudo random string
     *
     * @param string $codeLen
     * @return string 
     */
    private function generateRandomNumber($codeLen) {
        if (file_exists('/dev/urandom')) { // Get 100 bytes of random data
            $randomData = file_get_contents('/dev/urandom', false, null, 0, 100) . uniqid(mt_rand(), true);
        } else {
            $randomData = mt_rand() . mt_rand() . mt_rand() . mt_rand() . microtime(true) . uniqid(mt_rand(), true);
        }
        return substr(hash('sha512', $randomData), 0, $codeLen);
    }

}

