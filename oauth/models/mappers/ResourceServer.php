<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResourceServer
 *
 * @author andou
 */
class Oauth_Mapper_ResourceServer {
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
            $this->setDbTable('Oauth_Model_DbTable_ResourceServer');
        }
        return $this->_dbTable;
    }
    

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
