<?php

/**
 * Class Wallet_Model_Db_Table_Wallet
 */
class Wallet_Model_Db_Table_Transactions extends Core_Model_Db_Table
{
    /**
     * @var string 
     */
    protected $_name = 'wallet_transactions';

    /**
     * @var string 
     */
    protected $_primary = 'wallet_transaction_id';
    
}