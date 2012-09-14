<?php

/**
 * 
 * ResourceServer.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements a Resource Server Model Mapper
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Mapper_ResourceServer extends Oauth_Mapper_Abstract {

    /**
     * This object constructor
     * 
     */
    public function __construct() {
        $this->table_name = 'Oauth_Model_DbTable_ResourceServer';
    }

    /**
     * Retrieves a Resource Server from the DB by ID
     *
     * @param string $id
     * @return Oauth_Model_ResourceServer 
     */
    public function find($id) {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $client = new Oauth_Model_ResourceServer();
        $client->setId($row->resource_server_id)
                ->setName($row->resource_server_name)
                ->setSecret($row->resource_server_secret)
                ->setType($row->reference_type)
                ->setUri($row->resource_server_endpoint_uri);
        return $client;
    }

}
