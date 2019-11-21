<?php
class WalletYandexPS_Mobile_YandexController extends Application_Controller_Mobile_Default {

	/*
		Generate checkout data
	*/
	public function createformAction() {
		/*ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);	*/
		if ($params = Zend_Json::decode($this->getRequest()->getRawBody())) {
			$data['params'] = $params;
			$wallet = (new Wallet_Model_Wallet())->find($params['wallet_id']);
			if ($wallet->getId()) {

				$model = new WalletYandexPS_Model_PaymentMethodsYandex();
				$model->find(['wallet_id'=>$wallet->getId()]);
				if ($model->getId()) {
				
					//Создадим запись в истории
					$history = new Wallet_Model_PaymentHistory();
					$history
						->setWalletId($wallet->getId())
						->setWalletCustomerId($params['wallet_customer_id'])
						->setSumm($params['amount'])
						->setCode('yandex')
						->setComplete(0)
						->save();
				
				
					$data['yandex'] = $model->getData();
					$data['currency'] = Core_Model_Language::getCurrentCurrency()->getShortName();

					//Include library
					require_once($_SERVER['DOCUMENT_ROOT'].'/app/local/modules/WalletYandexPS/lib/yandex/lib/autoload.php');
					
					$client = new YandexCheckout\Client();
					$client->setAuth($model->getShopId(), $model->getSecretKey());
					$idempotenceKey = uniqid('', true);

					//Запрос
					try {
						$response = $client->createPayment(
							array(
								'amount' => array(
									'value' => $params['amount'],
									'currency' => Core_Model_Language::getCurrentCurrency()->getShortName(),
								),
								"capture"=> true,
								'confirmation' => array(
									'type' => 'redirect',
									'return_url' => parent::getUrl('walletyandexps/mobile_yandex/return', array('value_id' => $params['value_id'], 'wallet_id' => $params['wallet_id'],"wallet_customer_id"=>$params['wallet_customer_id'],"wallet_history_id"=>$history->getid(),'sb-token' => Zend_Session::getId())),
								),
								'description' => __("Deposit funds in the wallet") . " #" .$history->getId(),
							),
							$idempotenceKey
						);					
						$data['payment'] = $response;
						$data['payment_url']=$response->confirmation->confirmation_url;
						$data['success']=true;
						$history->setPaymentUrl($response->confirmation->confirmation_url)->setData('payment_id',$response->id)->save();						
					}
					catch(YandexCheckout\Common\Exceptions\BadApiRequestException $ex ) {
						$data['success']=false;
						$data['error_yandex']=Zend_Json::decode($ex->getResponseBody());
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
	
	//Проверка платежа
	public function returnAction() {
		if ($params = $this->getRequest()->getParams()) {
			$data['params'] = $params;
			$history = new Wallet_Model_PaymentHistory();
			$history->find($params['wallet_history_id']);
			if ($history->getId()) {		
			
				//Найдем у нас транзакцию и пользователя и кошелек
				$wallet_customer = (new Wallet_Model_Customer())->find($params['wallet_customer_id']);
				$model = new WalletYandexPS_Model_PaymentMethodsYandex();
				$model->find(['wallet_id'=>$params['wallet_id']]);				
				if ($model->getId()) {
					//Проверим
					//Include library
					require_once($_SERVER['DOCUMENT_ROOT'].'/app/local/modules/WalletYandexPS/lib/yandex/lib/autoload.php');
					
					$client = new YandexCheckout\Client();
					$client->setAuth($model->getShopId(), $model->getSecretKey());					
					$payment = $client->getPaymentInfo($history->getData('payment_id'));
					$data['payment_data'] = $payment;
					if ($payment->status=="succeeded") {
						$history->setComplete(1)->save();
						$wallet_customer->addTransaction($history->getSumm(),"Yandex - ".__("Deposit funds in the wallet"),'in',0,$wallet_customer->getId());
						$this->_redirect('walletyandexps/mobile_yandex/result', array(
							'value_id' => $params['value_id'],
							'wallet_id' => $params['wallet_id'],
							'wallet_customer_id' => $params['wallet_customer_id'],
							'status' => 1,
						));							
					} else if ($payment->status=="waiting_for_capture" || $payment->status=="pending") {
						$history->setData('payment_message',__("Returned payment status:").$payment->status." ".__("Contact the administration to resolve the issue with payment."))->save();
						$data['yandex_errror'] = "payment status ".$payment->status;
						$this->_redirect('walletyandexps/mobile_yandex/result', array(
							'value_id' => $params['value_id'],
							'wallet_id' => $params['wallet_id'],
							'wallet_customer_id' => $params['wallet_customer_id'],
							'status' => -1,
						));						
					} else if ($payment->status=="canceled") {
						$history->setComplete(-1)->save();
						$data['yandex_errror'] = "payment status canceled";
						$this->_redirect('walletyandexps/mobile_yandex/result', array(
							'value_id' => $params['value_id'],
							'wallet_id' => $params['wallet_id'],
							'wallet_customer_id' => $params['wallet_customer_id'],
							'status' => -1,
						));						
					}
			
				} else {
					//транзакции нет такой
					$data['yandex_errror'] = "payment method not found";
					$history->setComplete(-1)->save();
					$this->_redirect('walletyandexps/mobile_yandex/result', array(
						'value_id' => $params['value_id'],
						'wallet_id' => $params['wallet_id'],
						'wallet_customer_id' => $params['wallet_customer_id'],
						'status' => -1,
					));					
				
				}
			} else {
				//транзакции нет такой
				$data['yandex_errror'] = "transaction_id not found";
				$history->setComplete(-1)->save();
				$this->_redirect('walletyandexps/mobile_yandex/result', array(
					'value_id' => $params['value_id'],
					'wallet_id' => $params['wallet_id'],
					'wallet_customer_id' => $params['wallet_customer_id'],
					'status' => -1,
				));			
			
			}
		}
		$this->_sendHtml($data);
	}
}