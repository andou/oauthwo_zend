<?php
/**
 * 
 * RefreshToken.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Builder class to create refresh tokens
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Builder_RefreshToken{

    /**
     * Creates a refresh token 
     *
     * @param Oauth_Model_Client $client
     * @param string $scopes
     * @param Oauth_Model_ResourceOwner $resource_owner
     * @return Oauth_Model_RefreshToken 
     */
    public function create(Oauth_Model_Client $client, $scopes, Oauth_Model_ResourceOwner $resource_owner) {

        $code = $this->generateRandomNumber(20);


        $refresh_token = new Oauth_Model_RefreshToken();

        $refresh_token->setCode($code);
        $refresh_token->setClient($client);
        $refresh_token->setScopes($scopes);
        $refresh_token->setResourceOwnerId($resource_owner->getId());

        //Saving to the DB - should we?
        $refresh_tokenMapper= new Oauth_Mapper_RefreshToken();
        $refresh_tokenMapper->save($refresh_token);

        return $refresh_token;
    }

    /**
     * Retrieve a Refresh Token from the DB
     * 
     * @param string $code
     * @return Oauth_Model_RefreshToken
     */
    public function retrieve($code) {
        $codeMapper = new Oauth_Mapper_RefreshToken();
        return $codeMapper->find($code);
    }

    /**
     * Consumes a Refresh Token deleting it from the DB
     *
     * @param string $code
     * @return Oauth_Model_RefreshToken
     */
    public function consume($code) {        
        $codeMapper = new Oauth_Mapper_RefreshToken();
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

