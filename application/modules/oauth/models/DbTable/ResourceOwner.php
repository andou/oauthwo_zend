<?php

/**
 * 
 * ResourceOwner.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements a Resource Owner DataBase Table
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Model_DbTable_ResourceOwner extends Zend_Db_Table_Abstract {

    /**
     * db table name
     * 
     * @var string
     */
    protected $_name = 'user';

    /**
     * primary column name
     *
     * @var string
     */
    protected $_primary = 'user_id';

}

