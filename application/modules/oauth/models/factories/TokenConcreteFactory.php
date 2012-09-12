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
class Oauth_Factory_TokenConcreteFactory  {

    /**
     * Creates a Token
     *
     * @return Oauth_Model_IToken
     */
    public function create(Oauth_Model_ResourceOwner $resource_owner, $scopes) {

        /**
         * per il momento crea un token in maniera completamente randomica
         * si tratta della generazione di un identificativo sul DB.
         * 
         * Può essere cambiato a piacimento
         */
        $code = $this->generateRandomNumber(40);
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


        //$access_token->setCode(print_r( /*$token_data*/json_encode($code)  , true));
        $access_token->setCode(base64_encode(json_encode($code)));
        $access_token->setType('bearer');
        $access_token->setExpireDate(time() + ACCESS_TOKEN_VALIDITY);

        //return "stocazzo, bello!";

        return $access_token;
    }

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

    private function generateRandomNumber($codeLen) {
        if (file_exists('/dev/urandom')) { // Get 100 bytes of random data
            $randomData = file_get_contents('/dev/urandom', false, null, 0, 100) . uniqid(mt_rand(), true);
        } else {
            $randomData = mt_rand() . mt_rand() . mt_rand() . mt_rand() . microtime(true) . uniqid(mt_rand(), true);
        }
        return substr(hash('sha512', $randomData), 0, $codeLen);
    }

}

