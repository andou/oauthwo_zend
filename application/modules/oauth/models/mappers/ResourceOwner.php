<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthorizationCodeMapper
 *
 * @author andou
 */
class Oauth_Mapper_ResourceOwner {

    protected $_dbTable;

    public function setDbTable($dbTable) {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable() {
        if (null === $this->_dbTable) {
            $this->setDbTable('Oauth_Model_DbTable_ResourceOwner');
        }
        return $this->_dbTable;
    }

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
        $references=  array();
        
        for ($i = 0; $i < $rows->count(); $i++) {
            $row = $rows->current();
            $references[$row->resource_server_id] = $row->user_reference;
            $rows->next();           
        }
        
        $resource_owner->setReferences($references);


        return $resource_owner;
    }

}

?>
