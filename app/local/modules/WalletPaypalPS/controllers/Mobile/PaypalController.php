<?php
class WalletPaypalPS_Mobile_PaypalController extends Application_Controller_Mobile_Default {

	/*
		Generate checkout data
	*/
	public function createformAction() {
		if ($params = Zend_Json::decode($this->getRequest()->getRawBody())) {
			$data['params'] = $params;
			$wallet = (new Wallet_Model_Wallet())->find($params['wallet_id']);
			if ($wallet->getId()) {

				$model = new WalletPaypalPS_Model_PaymentMethodsPaypal();
				$model->find(['wallet_id'=>$wallet->getId()]);
				if ($model->getId()) {
				
					//Создадим запись в истории
					$history = new Wallet_Model_PaymentHistory();
					$history
						->setWalletId($wallet->getId())
						->setWalletCustomerId($params['wallet_customer_id'])
						->setSumm($params['amount'])
						->setCode('paypal')
						->setComplete(0)
						->save();
				
				
					$data['paypal'] = $model->getData();
					$data['currency'] = Core_Model_Language::getCurrentCurrency()->getShortName();
					$is_testing = false;
					if ($model->getIsTesting()=='1') $is_testing = true;
					//Include library
					require_once($_SERVER['DOCUMENT_ROOT'].'/app/local/modules/WalletPaypalPS/lib/paypal/autoload.php');
					
					//Login
					$apiContext = new \PayPal\Rest\ApiContext(
					  new \PayPal\Auth\OAuthTokenCredential(
						$model->getUsername(),
						$model->getPassword()
					  )
					);
					if (!$is_testing) {
						$apiContext->setConfig(
							  array(
								'mode' => 'live',
							  )
						);
					}
					$payer = new \PayPal\Api\Payer();
					$payer->setPaymentMethod('paypal');
					
					
					$amount = new \PayPal\Api\Amount();
					$amount->setTotal($params['amount']);
					$amount->setCurrency(Core_Model_Language::getCurrentCurrency()->getShortName());
					$transaction = new \PayPal\Api\Transaction();
					$transaction
						->setAmount($amount)
						->setDescription(__("Deposit funds in the wallet") . "#" .$history->getId())
						->setItemList($itemList);
					
					$redirectUrls = new \PayPal\Api\RedirectUrls();
					$redirectUrls
						->setReturnUrl(parent::getUrl('walletpaypalps/mobile_paypal/return', array('value_id' => $params['value_id'], 'wallet_id' => $params['wallet_id'],"wallet_customer_id"=>$params['wallet_customer_id'],"wallet_history_id"=>$history->getid(),'sb-token' => Zend_Session::getId())))
						->setCancelUrl(parent::getUrl('walletpaypalps/mobile_paypal/cancel', array('value_id' => $params['value_id'], 'wallet_id' => $params['wallet_id'],"wallet_customer_id"=>$params['wallet_customer_id'],"wallet_history_id"=>$history->getid(),'sb-token' => Zend_Session::getId())));			

					$payment = new \PayPal\Api\Payment();
					$payment->setIntent('sale')
						->setPayer($payer)
						->setTransactions(array($transaction))
						->setRedirectUrls($redirectUrls);
						
					try {
						$payment->create($apiContext);
						$data['payment']=$payment;
						$data['payment_url']=$payment->getApprovalLink();
						$data['success']=true;
						$history->setPaymentUrl($payment->getApprovalLink())->save();
					}
					catch (\PayPal\Exception\PayPalConnectionException $ex) {

						$data['success']=false;
						$data['error_paypal']=Zend_Json::decode($ex->getData());
						$history->setComplete(-1)->save();
					}						
						
				} else {
					$data = array('error' => 1, 'message' => 'An error occurred during process. Please try again later.');
					$history->setComplete(-1)->save();
				}
			} else {
				$data = array('error' => 1, 'message' => 'An error occurred during process. Please try again later.');
			}
			
		}else {
				$data = array('error' => 1, 'message' => 'An error occurred during process. Please try again later.');
		}

		$this->_sendHtml($data);
	}
	
	
	//cancel listener
	public function cancelAction() {
		$data = array();
		if ($params = $this->getRequest()->getParams()) {
			$data['params'] = $params;
			$data['type'] = 'cancel';
			$history = new Wallet_Model_PaymentHistory();
			$history->find($params['wallet_history_id']);
			if ($history->getId()) {
				$history->setComplete(-1)->save();
				$this->_redirect('walletpaypalps/mobile_paypal/result', array(
					'value_id' => $params['value_id'],
					'wallet_id' => $params['wallet_id'],
					'wallet_customer_id' => $params['wallet_customer_id'],
					'status' => -1,
				));			
			}
		}
		$this->_sendHtml($data);
	}
	
	//return listener
	public function returnAction() {
		$data = array();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);		
		if ($params = $this->getRequest()->getParams()) {
			$data['params'] = $params;
			$data['type'] = 'cancel';
			$history = new Wallet_Model_PaymentHistory();
			$history->find($params['wallet_history_id']);
			if ($history->getId()) {

				//Найдем у нас транзакцию и пользователя и кошелек
				$wallet_customer = (new Wallet_Model_Customer())->find($params['wallet_customer_id']);
				$model = new WalletPaypalPS_Model_PaymentMethodsPaypal();
				$model->find(['wallet_id'=>$params['wallet_id']]);				
				if ($model->getId()) {
					//Подтвердим
					require_once($_SERVER['DOCUMENT_ROOT'].'/app/local/modules/WalletPaypalPS/lib/paypal/autoload.php');
					$is_testing = false;
					if ($model->getIsTesting()=='1') $is_testing = true;				
					$apiContext = new \PayPal\Rest\ApiContext(
					  new \PayPal\Auth\OAuthTokenCredential(
						$model->getUsername(),
						$model->getPassword()
					  )
					);
					if (!$is_testing) {
						$apiContext->setConfig(
							  array(
								'mode' => 'live',
							  )
						);
					}

					$payment = \PayPal\Api\Payment::get($params['paymentId'], $apiContext);
					$execution = new \PayPal\Api\PaymentExecution(); 
					$execution->setPayerId($params['PayerID']);
					
					try {
						$result = $payment->execute($execution, $apiContext);
						$data['paypal_payment_id'] = $payment->getId();
						try {
							$payment = \PayPal\Api\Payment::get($params['paymentId'], $apiContext);
							
							//Платеж в системе есть, проведем у насс
							$history->setComplete(1)->save();
							$wallet_customer->addTransaction($history->getSumm(),"Paypal - ".__("Deposit funds in the wallet"),'in',0,$wallet_customer->getId());
							$this->_redirect('walletpaypalps/mobile_paypal/result', array(
								'value_id' => $params['value_id'],
								'wallet_id' => $params['wallet_id'],
								'wallet_customer_id' => $params['wallet_customer_id'],
								'status' => 1,
							));	
						} catch (Exception $ex) {
							//error
							$data['paypal_errror'] = $ex;
							$history->setComplete(-1)->save();
							$this->_redirect('walletpaypalps/mobile_paypal/result', array(
								'value_id' => $params['value_id'],
								'wallet_id' => $params['wallet_id'],
								'wallet_customer_id' => $params['wallet_customer_id'],
								'status' => -1,
							));								
						}
					}
					catch (Exception $ex) {
						//error
						$data['paypal_errror'] = $ex;
						$history->setComplete(-1)->save();
						$this->_redirect('walletpaypalps/mobile_paypal/result', array(
							'value_id' => $params['value_id'],
							'wallet_id' => $params['wallet_id'],
							'wallet_customer_id' => $params['wallet_customer_id'],
							'status' => -1,
						));							
					}
				
				
				}
			}
		}
		$this->_sendHtml($data);
	}	
}