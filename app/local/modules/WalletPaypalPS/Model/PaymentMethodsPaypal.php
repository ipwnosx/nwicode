<?php

/**
 * Class WalletPaypalPS_Model_PaymentMethodsPaypal
 *
 */
class WalletPaypalPS_Model_PaymentMethodsPaypal extends Core_Model_Default
{
    /**
     * Wallet_Model_Customer constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->_db_table = 'WalletPaypalPS_Model_Db_Table_PaymentMethodsPaypal';
        return $this;
    }
}