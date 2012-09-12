<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ClientMapper
 *
 * @author andou
 */
class Oauth_Mapper_Client {
    
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
            $this->setDbTable('Oauth_Model_DbTable_Client');
        }
        return $this->_dbTable;
    }
    

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

?>
