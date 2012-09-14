<?php

/**
 * 
 * ResourceOwner.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements a Resource Owner Model Mapper
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Mapper_ResourceOwner extends Oauth_Mapper_Abstract {

    /**
     * This object constructor
     * 
     */
    public function __construct() {
        $this->table_name = 'Oauth_Model_DbTable_ResourceOwner';
    }

    /**
     * Retrieve a resource owner from session id
     *
     * @return Oauth_Model_ResourceOwner|boolean 
     */
    public function fromSession() {
        $auth = Zend_Auth::getInstance();

        if ($auth->hasIdentity()) {

            return $this->find($auth->getIdentity());

            $client = new Oauth_Model_ResourceOwner();
            $identity = $auth->getIdentity();
            $client->setId($identity);
            return $client;
        }

        return FALSE;
    }

    /**
     * Retrieves a Resource Owner from the DB by ID
     *
     * @param string $id
     * @return Oauth_Model_ResourceOwner 
     */
    public function find($id) {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();

        $resource_owner = new Oauth_Model_ResourceOwner();
        $resource_owner->setId($row->user_id);

        $table = new Oauth_Model_DbTable_ResourceOwnerReference();
        $select = $table->select();
        $select->where('user_id = ?', $resource_owner->getId());

        $rows = $table->fetchAll($select);
        $references = array();

        for ($i = 0; $i < $rows->count(); $i++) {
            $row = $rows->current();
            $references[$row->resource_server_id] = $row->user_reference;
            $rows->next();
        }

        $resource_owner->setReferences($references);


        return $resource_owner;
    }

}
