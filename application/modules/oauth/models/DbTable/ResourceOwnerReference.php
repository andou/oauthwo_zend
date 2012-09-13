<?php

/**
 * 
 * ResourceOwnerReference.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements the Resource Owner Identity Equivalence Class reference Table
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Model_DbTable_ResourceOwnerReference extends Zend_Db_Table_Abstract {

    /**
     * db table name
     * @var type 
     */
    protected $_name = 'user_reference';

    /**
     * primary columns names
     * 
     * @var array
     */
    protected $_primary = array('user_id', 'resource_server_id');

}

