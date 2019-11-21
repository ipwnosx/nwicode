<?php

/**
 * Class Wallet_Model_Db_Table_Bill
 */
class Wallet_Model_Db_Table_Bill extends Core_Model_Db_Table
{
    /**
     * @var string 
     */
    protected $_name = 'wallet_bills';

    /**
     * @var string 
     */
    protected $_primary = 'wallet_bill_id';
    
}