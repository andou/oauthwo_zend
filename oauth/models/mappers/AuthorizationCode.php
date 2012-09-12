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
class Oauth_Mapper_AuthorizationCode {

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
            $this->setDbTable('Oauth_Model_DbTable_AuthorizationCode');
        }
        return $this->_dbTable;
    }

    public function save(Oauth_Model_AuthorizationCode $authorizationCode) {
        $data = array(
            'authorization_code' => $authorizationCode->getCode(),
            'client_id' => $authorizationCode->getClientId(),
            'resource_owner_id'=>$authorizationCode->getResourceOwnerId(),
            'scopes' => $authorizationCode->getScopes(),
//            'generation_timestamp' => time(),
        );

        $this->getDbTable()->insert($data);
    }
    
    

    public function find($code) {
        $result = $this->getDbTable()->find($code);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $clientMapper = new Oauth_Mapper_Client();
        $client = $clientMapper->find($row->client_id);        
        
        $code = new Oauth_Model_AuthorizationCode();
        
        $code->setCode($row->authorization_code)
                ->setClient($client)
                ->setResourceOwnerId($row->resource_owner_id)
                ->setScopes($row->scopes)
                ->setCreated($row->generation_timestamp);
        
        return $code;
    }
    
    public function delete($code){
        
        $result = $this->getDbTable()->find($code);
        if (0 == count($result)) {
            return;
        }
        
        $row = $result->current();
        $row->delete();
        
    }
    

}

?>
