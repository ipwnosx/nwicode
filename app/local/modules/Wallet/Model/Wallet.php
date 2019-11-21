<?php

/**
 * Class Wallet_Model_Wallet
 *
 */
class Wallet_Model_Wallet extends Core_Model_Default
{
    /**
     * Wallet_Model_Wallet constructor.
     * @param array $params
     */
	 
	protected $_payout_methods;
	protected $_payout_requests;
	protected $_customers;
	protected $_transactions;
	protected $_bills;
	protected $_addfundshistory;
	 
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->_db_table = 'Wallet_Model_Db_Table_Wallet';
        return $this;
    }
	
	//Прозрачно создаем пользователя
	public function createCustomer($customer_id) {
		if ($this->getSession()->getAppId()) $app_id = $this->getSession()->getAppId(); else $app_id=$this->getApplication()->getId();

		$db =Zend_Db_Table_Abstract::getDefaultAdapter();
		$stmt = $db->query(
			'SELECT * FROM application_option WHERE code = ?',
			array('wallet')
		);
		$option_id = $stmt->fetch()['option_id'];
		$stmt = $db->query(
			'SELECT * FROM application_option_value WHERE option_id = ? and app_id = ?',
			array($option_id, $app_id)
		);
		$value_id = $stmt->fetch()['value_id'];
		$wallet = new Wallet_Model_Wallet();
		$wallet->find(array("value_id" => $value_id));
		
		//Если пользователя нет, то заведем его
		$customer = (new Customer_Model_Customer())->find($customer_id);
		$wallet_customer = (new Wallet_Model_Customer())->find(array('wallet_id'=>$wallet->getId(),'customer_id'=>$customer_id));
		if (!$wallet_customer->getId()) {
			$wallet_customer
				->setCustomerId($customer->getId())
				->setWalletId($wallet->getId())
				->setIsBlocked(0)
				->setEmail($customer->getEmail())
				->save();
			if ($wallet->getSignUpBonus()>0) $wallet_customer->addTransaction($wallet->getSignUpBonus(),__("Registration bonus."),"in");
		}
		return $wallet_customer;
	}
	
	//удобный метода для зачисления средств на счет извне
	public function addFunds($customer_id,$funds,$comment) {
	
		if ($this->getSession()->getAppId()) $app_id = $this->getSession()->getAppId(); else $app_id=$this->getApplication()->getId();

		$db =Zend_Db_Table_Abstract::getDefaultAdapter();
		$stmt = $db->query(
			'SELECT * FROM application_option WHERE code = ?',
			array('wallet')
		);
		$option_id = $stmt->fetch()['option_id'];
		$stmt = $db->query(
			'SELECT * FROM application_option_value WHERE option_id = ? and app_id = ?',
			array($option_id, $app_id)
		);
		$value_id = $stmt->fetch()['value_id'];
		$wallet = new Wallet_Model_Wallet();
		$wallet->find(array("value_id" => $value_id));
		
		
		//Если пользователя нет, то заведем его
		$customer = (new Customer_Model_Customer())->find($customer_id);
		$wallet_customer = (new Wallet_Model_Customer())->find(array('wallet_id'=>$wallet->getId(),'customer_id'=>$customer_id));
		if (!$wallet_customer->getId()) {
			$wallet_customer
				->setCustomerId($customer->getId())
				->setWalletId($wallet->getId())
				->setIsBlocked(0)
				->setEmail($customer->getEmail())
				->save();
			if ($wallet->getSignUpBonus()>0) $wallet_customer->addTransaction($wallet->getSignUpBonus(),__("Registration bonus."),"in");
		}
	
		//Добавим транзакцию
		$wallet_customer->addTransaction($funds,$comment,"in");
		
		
		return $wallet->getId();
	
	}
	
    public function getPayoutMethods()
    {

        if (!$this->_payout_methods) {
            $payout_methods = new Wallet_Model_PayoutMethods();
			$this->_payout_methods = array();
			foreach ($payout_methods->findAll(['wallet_id' => $this->getId()]) as $pm) {
				$this->_payout_methods[]=(new Wallet_Model_PayoutMethods())->find($pm->getId());
			}
        }
        return $this->_payout_methods;

    }
	
    public function getCustomers()
    {

        if (!$this->_customers) {
            $customers = new Wallet_Model_Customer();
			$this->_customers = array();
			foreach ($customers->findAll(['wallet_id' => $this->getId()]) as $cw) {
				$this->_customers[]=(new Wallet_Model_Customer())->find($cw->getId());
			}
        }
        return $this->_customers;

    }
	
	
    public function getTransactions()
    {

		if (!$this->_transactions) {
            $transactions = new Wallet_Model_Transactions();
			$this->_transactions = array();
			foreach ($transactions->findAll(['wallet_id' => $this->getWalletId()]) as $pm) {
				$this->_transactions[]=(new Wallet_Model_Transactions())->find($pm->getId());
			}			
		}
		return $this->_transactions;

    }
	
    public function getTransactionsLimit($params)
    {

		if (!$this->_transactions) {
            $transactions = new Wallet_Model_Transactions();
			$this->_transactions = array();
			foreach ($transactions->findAll($params) as $pm) {
				$this->_transactions[]=(new Wallet_Model_Transactions())->find($pm->getId());
			}			
		}
		return $this->_transactions;

    }	
	
    public function getBills()
    {

		if (!$this->_bills) {
            $bills = new Wallet_Model_Bill();
			$this->_bills = array();
			foreach ($bills->findAll(['wallet_id' => $this->getWalletId()]) as $pm) {
				$this->_bills[]=(new Wallet_Model_Bill())->find($pm->getId());
			}			
		}
		return $this->_bills;

    }	
	
	public function getPayoutRequests() {
	
		if (!$this->_payout_requests) {
            $payout_requests = new Wallet_Model_PayoutRequest();
			$this->_payout_requests = array();
			foreach ($payout_requests->findAll(['wallet_id' => $this->getWalletId()]) as $pm) {
				$this->_payout_requests[]=(new Wallet_Model_PayoutRequest())->find($pm->getId());
			}			
		}
		return $this->_payout_requests;
	}	
	
	public function getCustomer($customer_id) {
		$customer = (new Wallet_Model_Customer())->find(['wallet_id' => $this->getId(),'customer_id'=>$customer_id]);
	
		return $customer;
	}
	
	
	public function getAddFundsHsitory() {
	
		if (!$this->_addfundshistory) {
            $addfundshistory = new Wallet_Model_PaymentHistory();
			$this->_addfundshistory = array();
			foreach ($addfundshistory->findAll(['wallet_id' => $this->getWalletId()]) as $pm) {
				$this->_addfundshistory[]=(new Wallet_Model_PaymentHistory())->find($pm->getId());
			}			
		}
		return $this->_addfundshistory;	
	}
}
