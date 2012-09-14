<?php

/**
 * 
 * Client.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements a Client Model Mapper
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Mapper_Client extends Oauth_Mapper_Abstract {

    /**
     * This object constructor
     * 
     */
    public function __construct() {
        $this->table_name = 'Oauth_Model_DbTable_Client';
    }

    /**
     * Retrieves a Client from the DB by ID
     *
     * @param String $id
     * @return Oauth_Model_Client 
     */
    public function find($id) {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $client = new Oauth_Model_Client();
        $client->setId($row->client_id)
                ->setName($row->client_name)
                ->setSecret($row->client_secret)
                ->setType($row->client_type)
                ->setRedirectUri($row->redirect_uri);
        return $client;
    }

}
