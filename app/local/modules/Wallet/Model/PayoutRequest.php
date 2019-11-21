<?php

/**
 * Class Wallet_Model_PayoutMethods
 *
 */
class Wallet_Model_PayoutRequest extends Core_Model_Default
{
    /**
     * Wallet_Model_Customer constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->_db_table = 'Wallet_Model_Db_Table_PayoutRequest';
        return $this;
    }
	
	public function getCustomer() {
		$customer = (new Wallet_Model_Customer())->find($this->getWalletCustomerId());
	
		return $customer;
	}	
}