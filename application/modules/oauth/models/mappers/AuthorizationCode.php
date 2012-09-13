<?php
/**
 * 
 * AuthorizationCode.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements an Authorization Code Mapper
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Mapper_AuthorizationCode extends Oauth_Mapper_Abstract{


    /**
     * This object constructor
     * 
     */
    public function __construct() {
        $this->table_name = 'Oauth_Model_DbTable_AuthorizationCode';
    }

    
    /**
     * Saves an Authorization Code in the DB
     *
     * @param Oauth_Model_AuthorizationCode $authorizationCode 
     */
    public function save(Oauth_Model_AuthorizationCode $authorizationCode) {
        $data = array(
            'authorization_code' => $authorizationCode->getCode(),
            'client_id' => $authorizationCode->getClientId(),
            'resource_owner_id'=>$authorizationCode->getResourceOwnerId(),
            'scopes' => $authorizationCode->getScopes(),
        );

        $this->getDbTable()->insert($data);
    }
    
    
    /**
     * Retrieves an authorization code from the DB by code
    *
    * @param string $code
    * @return Oauth_Model_AuthorizationCode 
    */
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
    
    /**
     * Deletes an authorization code by code
     *
     * @param string $code
     * @return int
     */
    public function delete($code){
        
        $result = $this->getDbTable()->find($code);
        if (0 == count($result)) {
            return;
        }
        
        $row = $result->current();
        $row->delete();
        
    }
    

}

