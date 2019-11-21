<?php

/**
 * Class Wallet_Model_PayoutMethods
 *
 */
class Wallet_Model_PayoutMethods extends Core_Model_Default
{
    /**
     * Wallet_Model_Customer constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->_db_table = 'Wallet_Model_Db_Table_PayoutMethods';
        return $this;
    }
}