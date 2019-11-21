<?php

/**
 * Class Wallet_Model_Customer
 *
 */
class Wallet_Model_Customer extends Core_Model_Default
{

	protected $_transactions;
	protected $_payout_requests;
	protected $_bills;
	protected $_addfundshistory;
    /**
     * Wallet_Model_Customer constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->_db_table = 'Wallet_Model_Db_Table_Customer';
        return $this;
    }
	
	
	//Сохраним транзакцию в кошелек
	public function addTransaction($transaction_amount,$description,$type='in',$from_customer = 0,$to_customer = 0,$os_transaction_id = 0) {
		//if ($transaction_amount==0) return;
		$score = $this->getScore();
		$transaction = new Wallet_Model_Transactions();
		$transaction->setData('wallet_id',$this->getWalletId());
		$transaction->setData('wallet_customer_id',$this->getId());
		$transaction->setData('summ',$score);
		$transaction->setData('operation_summ',$transaction_amount);
		$transaction->setData('summ_after',$transaction_amount+$score);
		$transaction->setData('type',$type);
		$transaction->setData('description',$description);
		$transaction->setData('complete',1);

		if ($from_customer!=0) $transaction->setData('from_customer_id',$from_customer);
		if ($to_customer!=0) $transaction->setData('to_customer_id',$to_customer);
		if ($os_transaction_id!=0) $transaction->setData('os_transaction_id',$os_transaction_id);
		
		//Совокупность условий, при которой происходит загузка баланса в кошелек, иными словами - пополнение.
		//Спишем нашу комиссию
		if ($from_customer==0 && $type=='in' && $os_transaction_id == 0) {
			$wallet = $this->getWallet();
			$upload_commission = (float)$wallet->getData("upload_commission");
			if ($upload_commission>0) {
				
				//Рассчитаем
				$upload_commission_summ = (abs($transaction_amount)/100)*$upload_commission;
				$transaction_amount = $transaction_amount-$upload_commission_summ;

				//Сохраним
				$transaction->setData('operation_summ',$transaction_amount);
				$transaction->setData('summ_after',$transaction_amount+$score);				
				$transaction->setData("comission_summ",$upload_commission_summ);
				$transaction->setTransactionNote(__("Commission"). " (".$upload_commission."%): ".round($upload_commission_summ,2));
				
			}
		}
		
		$transaction->save();		
		$this->setScore($transaction_amount+$score)->save();
		return $transaction->getId();
	}
	
	public function getTransactions() {
	
		if (!$this->_transactions) {
            $transactions = new Wallet_Model_Transactions();
			$this->_transactions = array();
			foreach ($transactions->findAll(['wallet_id' => $this->getWalletId(),'wallet_customer_id'=>$this->getId()]) as $pm) {
				$this->_transactions[]=(new Wallet_Model_Transactions())->find($pm->getId());
			}			
		}
		return $this->_transactions;
	}
	
	public function getPayoutRequests() {
	
		if (!$this->_payout_requests) {
            $payout_requests = new Wallet_Model_PayoutRequest();
			$this->_payout_requests = array();
			foreach ($payout_requests->findAll(['wallet_id' => $this->getWalletId(),'wallet_customer_id'=>$this->getId()]) as $pm) {
				$this->_payout_requests[]=(new Wallet_Model_PayoutRequest())->find($pm->getId());
			}			
		}
		return $this->_payout_requests;
	}
	
	public function getAddFundsHistory() {
	
		if (!$this->_addfundshistory) {
            $addfundshistory = new Wallet_Model_PaymentHistory();
			$this->_addfundshistory = array();
			foreach ($addfundshistory->findAll(['wallet_id' => $this->getWalletId(),'wallet_customer_id'=>$this->getId()]) as $pm) {
				$this->_addfundshistory[]=(new Wallet_Model_PaymentHistory())->find($pm->getId());
			}			
		}
		return $this->_addfundshistory;
	}
	
	public function addPayoutRequest($transaction_amount,$description,$transaction_id, $payout_method_id,$status='pending') {
		$payout = new Wallet_Model_PayoutRequest();
		$payout->setData('payout_method',$payout_method_id);
		$payout->setData('status',$status);
		$payout->setData('summ',$transaction_amount);
		$payout->setData('customer_info',$description);
		$payout->setData('transaction_id',$transaction_id);
		$payout->setData('wallet_customer_id',$this->getId());
		$payout->setData('wallet_id',$this->getWalletId());
		
		$payout_method = (new Wallet_Model_PayoutMethods())->find($payout_method_id);
		$payout->setData('payout_method_title',$payout_method->getTitle());
		
		$payout->save();
		
		return $payout->getId();
	}
	
	public function cancelPayout($id) {
		$payout = new Wallet_Model_PayoutRequest();
		$payout->find($id);
		if ($payout->getId()) {
			$payout->setStatus('cancel');
			$payout->save();
			$amount = $payout->getSumm();
			$this->addTransaction($amount,__("Cancel payout"),$type='in');
		}
		
	}
	
	public function createBill($amount,$title,$description,$bill_source = "",$command_complete="",$command_cancel="",$complete_text="",$cancel_text="") {
		$bill = new Wallet_Model_Bill();
		$bill->setData('wallet_customer_id',$this->getId());
		$bill->setData('wallet_id',$this->getWalletId());	
		$bill->setData('title',$title);	
		$bill->setData('description',$description);	
		$bill->setData('bill_source',$bill_source);	
		$bill->setData('summ',$amount);	
		$bill->setData('command_complete',$command_complete);	
		$bill->setData('command_cancel',$command_cancel);	
		$bill->setData('cancel_text',$cancel_text);	
		$bill->setData('complete_text',$complete_text);	
		$bill->save();
		return $bill->getId();
	}

	public function getBills() {
	
		if (!$this->_bills) {
            $bills = new Wallet_Model_Bill();
			$this->_bills = array();
			foreach ($bills->findAll(['wallet_id' => $this->getWalletId(),'wallet_customer_id'=>$this->getId()]) as $pm) {
				$this->_bills[]=(new Wallet_Model_Bill())->find($pm->getId());
			}			
		}
		return $this->_bills;
	}
	
	public function acceptBill($bill_id) {
		
		$bill = new Wallet_Model_Bill();
		$bill->find($bill_id);
		if ($bill->getId()) {
			$description = __("Payment invoice No")." ".$bill->getId()."\n";
			$description .= __("Bill source").":".$bill->getBillSource()."\n";
			$description .= __("Bill title").":".$bill->getTitle();
			
			//Проведем данные
			$command_complete = $bill->getCommandComplete();
			if (!empty($command_complete)) $command = @json_decode($command_complete,true);
			$bill->setData('command',$command);
			$bill->setData('command1',$command_complete);
			if (is_array($command) && count($command)>0) {
				$model = $command['model'];
				$args = $command['args'];
				$action = new $model();
				$action->acceptBill($args);
				//$bill
			}
			
			$t = $this->addTransaction($bill->getSumm()*(-1),$description,"out");
			$bill->setTransactionId($t)->setStatus("complete")->setOperationAt(Zend_Date::now()->toString('YYYY-MM-dd HH:mm:ss'))->save();
		}
		return $bill;
	}
	public function cancelBill($bill_id) {
		
		$bill = new Wallet_Model_Bill();
		$bill->find($bill_id);
		if ($bill->getId()) {
			//Проведем данные
			$command_cancel = $bill->getCommandCancel();
			if (!empty($command_cancel)) $command = @json_decode($command_cancel,true);
			$bill->setData('command',$command);
			if (is_array($command) && count($command)>0) {
				$model = $command['model'];
				$args = $command['args'];
				$action = new $model();
				$action->cancelBill($args);
				//$bill
			}		
			$bill->setStatus("cancel")->setOperationAt(Zend_Date::now()->toString('YYYY-MM-dd HH:mm:ss'))->save();
		}
		return $bill;
	}
	
	//Возвращает объект Кошелек
	public function getWallet() {
		return (new Wallet_Model_Wallet())->find($this->getWalletId());
	}
	
}