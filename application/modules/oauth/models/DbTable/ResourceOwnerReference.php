<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResourceOwnerReference
 *
 * @author andou
 */
class Oauth_Model_DbTable_ResourceOwnerReference extends Zend_Db_Table_Abstract
{

    protected $_name = 'user_reference';
    protected $_primary = array('user_id','resource_server_id');

}

