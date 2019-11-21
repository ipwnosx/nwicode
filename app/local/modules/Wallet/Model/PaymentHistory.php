<?php

/**
 * Class Wallet_Model_PaymentHistory
 *
 */
class Wallet_Model_PaymentHistory extends Core_Model_Default
{
    /**
     * Wallet_Model_PaymentHistory constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->_db_table = 'Wallet_Model_Db_Table_PaymentHistory';
        return $this;
    }
}