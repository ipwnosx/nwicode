<?php

/**
 * Class Wallet_Model_Transactions
 *
 */
class Wallet_Model_Transactions extends Core_Model_Default
{
    /**
     * Wallet_Model_Wallet constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->_db_table = 'Wallet_Model_Db_Table_Transactions';
        return $this;
    }
}
