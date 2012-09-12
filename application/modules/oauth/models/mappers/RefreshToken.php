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
class Oauth_Mapper_RefreshToken{

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
            $this->setDbTable('Oauth_Model_DbTable_RefreshToken');
        }
        return $this->_dbTable;
    }

    public function save(Oauth_Model_RefreshToken $refresh_token) {
        $data = array(
            'refresh_token' => $refresh_token->getCode(),
            'client_id' => $refresh_token->getClientId(),
            'resource_owner_id'=>$refresh_token->getResourceOwnerId(),
            'scopes' => $refresh_token->getScopes(),
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
        
        $code = new Oauth_Model_RefreshToken();
        
        $code->setCode($row->refresh_token)
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
