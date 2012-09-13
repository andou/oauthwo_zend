<?php

/**
 * 
 * Abstract.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements an Model Mapper
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
abstract class Oauth_Mapper_Abstract {

    /**
     * The table used by this mapper
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;

    /**
     * The table object to be used
     *
     * @var string
     */
    protected $table_name;
    
    /**
     * Sets the table to use
     *
     * @param mixed $dbTable
     * @return Oauth_Mapper_Abstract
     * @throws Exception 
     */
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
    
    /**
     * Returns the used db table
     *
     * @return mixed
     */
    public function getDbTable() {
        if (null === $this->_dbTable) {
            $this->setDbTable($this->table_name);
        }
        return $this->_dbTable;
    }

}
