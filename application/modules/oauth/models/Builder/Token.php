<?php
/**
 * 
 * Token.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Builder class to create Access Tokens
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Builder_Token  {

    
    /**
     * Creates the access token from a granting resource owner and a set of 
     * granted scopes.
     * 
     * Creates a JWE with a JWS of a JWT of claims as plaintext.
     * 
     * @param Oauth_Model_ResourceOwner $resource_owner
     * @param string $scopes
     * @return Oauth_Model_Token 
     */
    public function create(Oauth_Model_ResourceOwner $resource_owner, $scopes) {

        $code = array();


        $token_signer = new Oauth_Builder_JWS(PRIVATE_SIGN_KEY_LOCATION);
        $token_encrypter = new Oauth_Builder_JWE();

        $access_token = new Oauth_Model_Token();
        $token_data = $this->buildTokenData($scopes);

        foreach ($token_data as $k => $v) {
            $prn = $resource_owner->getReference($k);
            if ($prn) {
                $payload = array(
                    'iss' => 'AS_1',
                    'exp' => time() + ACCESS_TOKEN_VALIDITY,
                    'prn' => $prn,
                    'scope' => implode(" ",$v['scopes']),
                );
                $encoded_payload =  json_encode($payload);
                $signed_token = $token_signer->get_token($encoded_payload);
                $secret_key = $v['resource_server']->getSecret();
                $token_encrypter->set_key($secret_key);
                $encrypted_token = $token_encrypter->get_token($signed_token);
                
                $code[$v['resource_server']->getUri()] = array();
                $code[$v['resource_server']->getUri()]['scopes']=implode(" ",$v['scopes']);
                $code[$v['resource_server']->getUri()]['token_portion']=$encrypted_token;
            }
        }

        
        $access_token->setCode(base64_encode(json_encode($code)));
        $access_token->setType('bearer');
        $access_token->setExpireDate(time() + ACCESS_TOKEN_VALIDITY);
        
        return $access_token;
    }

    /**
     * Uses the set of scopes to retrieve data about the resource servers which 
     * delivers and understand each scope.
     *
     * @param string $scopes
     * @return array an associative array containing data to be encrypted in the access token
     */
    private function buildTokenData($scopes) {

        $scopes = explode(" ", trim($scopes));
        $datas = array();
        foreach ($scopes as $s) {
            $scopeMapper = new Oauth_Mapper_Scope();
            $scope = $scopeMapper->find($s);

            $serv_id = $scope->getResourceServer()->getId();
            if (array_key_exists($serv_id, $datas)) {
                $datas[$serv_id]['scopes'][] = $scope->getName();
            } else {
                $datas[$serv_id] = array();
                $datas[$serv_id]['scopes'] = array();
                $datas[$serv_id]['scopes'][] = $scope->getName();
                $datas[$serv_id]['resource_server'] = $scope->getResourceServer();
            }
        }

        return $datas;
    }



}

