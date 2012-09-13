<?php

/**
 * This class creates a Token given a Resource owner and a set of scopes
 *
 * @author antonio.pastorino@gmail.com
 */
class Oauth_Factory_TokenProducer  {

    
    /**
     * Creates the token
     * 
     * @param Oauth_Model_ResourceOwner $resource_owner
     * @param array $scopes
     * @return \Oauth_Model_Token 
     */
    public function create(Oauth_Model_ResourceOwner $resource_owner, $scopes) {

        $code = array();


        $token_signer = new Oauth_Factory_JSTProducer(PRIVATE_SIGN_KEY_LOCATION);
        $token_encrypter = new Oauth_Factory_JETProducer();

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
     * Build the token content
     *
     * @param array $scopes
     * @return array 
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

