<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthorizationCode
 *
 * @author andou
 */
class Oauth_Model_DbTable_AuthorizationCode extends Zend_Db_Table_Abstract
{

    protected $_name = 'authorization_codes';
    protected $_primary = 'authorization_code';

}

