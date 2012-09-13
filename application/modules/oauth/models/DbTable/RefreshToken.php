<?php

/**
 * 
 * RefreshToken.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements a Refresh Token DataBase Table
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Model_DbTable_RefreshToken extends Zend_Db_Table_Abstract {

    /**
     * db table name
     * @var string
     */
    protected $_name = 'refresh_tokens';

    /**
     * primary column name
     * 
     * @var string
     */
    protected $_primary = 'refresh_token';

}

