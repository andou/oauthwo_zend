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
 *  Implements a Refresh Token Mapper
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Mapper_RefreshToken extends Oauth_Mapper_Abstract {

    /**
     * This object constructor
     * 
     */
    public function __construct() {
        $this->table_name = 'Oauth_Model_DbTable_RefreshToken';
    }

    /**
     * Saves a Refresh token in the DB
     *
     * @param Oauth_Model_RefreshToken $refresh_token 
     */
    public function save(Oauth_Model_RefreshToken $refresh_token) {
        $data = array(
            'refresh_token' => $refresh_token->getCode(),
            'client_id' => $refresh_token->getClientId(),
            'resource_owner_id' => $refresh_token->getResourceOwnerId(),
            'scopes' => $refresh_token->getScopes(),
        );

        $this->getDbTable()->insert($data);
    }

    /**
     * Retrieves a refresh token from the db by code
     *
     * @param string $code
     * @return Oauth_Model_RefreshToken 
     */
    public function find($code) {
        $result = $this->getDbTable()->find($code);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $clientMapper = new Oauth_Mapper_Client();
        $client = $clientMapper->find($row->client_id);

        $code = new Oauth_Model_RefreshToken();

        $code->setCode($row->refresh_token)
                ->setClient($client)
                ->setResourceOwnerId($row->resource_owner_id)
                ->setScopes($row->scopes)
                ->setCreated($row->generation_timestamp);

        return $code;
    }

    /**
     * Deletes a refresh token from the DB by code
     *
     * @param string $code
     * @return int 
     */
    public function delete($code) {

        $result = $this->getDbTable()->find($code);
        if (0 == count($result)) {
            return;
        }

        $row = $result->current();
        $row->delete();
    }

}