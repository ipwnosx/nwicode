<?php
/*
	Данный метод хранит свои настройки в settings поле таблицы mcommerce_store_payment_method
*/
class Mcommerce_Model_Payment_Method_Walletmcommerce extends Mcommerce_Model_Payment_Method_Abstract {


	private $_api = '';
	private $_key = '';
	private $_url = '';
	private $_form_url = '';
	
    private $_supported_currency_codes = ['USD','RU','RUB' ];	

    /**
     * Mmobilcart_Model_Payment_Method_Yandex constructor.
     * @param array $params
     */
    public function __construct($params = []) {
        parent::__construct($params);
		$this->_code = 'walletmcommerce';
        $this->_db_table = 'Mcommerce_Model_Db_Table_Payment_Method_Walletmcommerce';
		return $this;
    }

    /**
     * @param $code
     * @return mixed|null
     */
	public function getCurrencyCode($code) {

		//if (isset($this->_supported_currency_codes[$code]))return $this->_supported_currency_codes[$code]; else return null;
		return $code;
	}

    /**
     * @return bool
     */
	public function isOnline() {
		return true;
	}

    /**
     * @param $valueId
     * @return array
     */
    public function getFormUris ($valueId) {
        return [
            'url' => null,
            'form_url' => parent::getPath('/walletmcommerce/mobile_wallet', array('value_id' => $valueId)),
            'url' => null,
			'code' => $this->_code
        ];
    }
	
    /**
     * @param null $id
     * @return bool
     */
    public function pay($id = null) {
        return true;
    }

    /**
     * @return string
     */
	public function getDebug() {
		Zend_Debug::dump($this);
		return 'd';
	}
	

}
