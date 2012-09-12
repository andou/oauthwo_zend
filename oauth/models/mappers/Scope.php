<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ScopeMapper
 *
 * @author andou
 */
class Oauth_Mapper_Scope {
        
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
            $this->setDbTable('Oauth_Model_DbTable_Scope');
        }
        return $this->_dbTable;
    }
    
    

    public function find($name) {
        $result = $this->getDbTable()->find($name);
        if (0 == count($result)) {
            return;
        }
        
        $server_mapper = new Oauth_Mapper_ResourceServer();
        
        $row = $result->current();
        $scope = new Oauth_Model_Scope();
        $scope->setName($row->scope_id)
                ->setDescription($row->scope_description)
                ->setResourceServer($server_mapper->find($row->resource_server_id));
        return $scope;
    }
}

?>
