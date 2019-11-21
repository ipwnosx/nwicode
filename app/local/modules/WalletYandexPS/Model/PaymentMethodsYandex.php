<?php

/**
 * Class WalletYandexPS_Model_PaymentMethodsYandex
 *
 */
class WalletYandexPS_Model_PaymentMethodsYandex extends Core_Model_Default
{
    /**
     * WalletYandexPS_Model_PaymentMethodsYandex constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->_db_table = 'WalletYandexPS_Model_Db_Table_PaymentMethodsYandex';
        return $this;
    }
}