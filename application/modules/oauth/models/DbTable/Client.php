<?php

/**
 * 
 * Client.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements a Client DataBase Table
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Model_DbTable_Client extends Zend_Db_Table_Abstract {

    /**
     * db table name
     *
     * @var string
     */
    protected $_name = 'client';

    /**
     * primary column name
     *
     * @var string
     */
    protected $_primary = 'client_id';

}

