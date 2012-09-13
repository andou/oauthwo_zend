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
 *  Implements a Resource Server DataBase Table
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Model_DbTable_ResourceServer extends Zend_Db_Table_Abstract {

    /**
     * db table name
     * 
     * @var string
     */
    protected $_name = 'resource_server';

    /**
     * primary column name
     *
     * @var string
     */
    protected $_primary = 'resource_server_id';

}
