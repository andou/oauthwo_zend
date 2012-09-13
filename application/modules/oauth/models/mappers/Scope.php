<?php

/**
 * 
 * Scope.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements a Scope Model Mapper
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Mapper_Scope extends Oauth_Mapper_Abstract {

    /**
     * This object constructor
     * 
     */
    public function __construct() {
        $this->table_name = 'Oauth_Model_DbTable_Scope';
    }

    /**
     * Retrieves a Scope from the DB by name
     *
     * @param string $name
     * @return Oauth_Model_Scope 
     */
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

