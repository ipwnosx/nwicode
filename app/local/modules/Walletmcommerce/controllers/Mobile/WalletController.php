<?php
class Walletmcommerce_Mobile_WalletController extends Mcommerce_Controller_Mobile_Default {

	//Сохраним заказ и перейдем в кошелек
	public function createorderAction() {
   
		$data['debug']=date("YmdHis");
		try {
            $request = $this->getRequest();
            if ($params = $request->getRawBody()) {
                $params = Nwicode_Json::decode($params);
            } else if ($data = $request->getPost()) {
                $params = $request->getPost();
            } else {
                $params = $request->getFilteredParams();
            }	
        
			//Начало стандартное
			$application = $this->getApplication();
			$method = $this->getCart()->getPaymentMethod();
			$customer = $this->getSession()->getCustomer();
		
			$cart = $this->getCart();
			$store = $cart->getStore();
			$valueId = $this->getCurrentOptionValue()->getId();
			$methodInstance = $method->getInstance()->find($methodId);
			$methodInstance->find($store->getId(),'store_id');
			$orderId = $this->getSession()->order_id;
			$currency_code = $method->getInstance()->getCurrencyCode(Core_Model_Language::getCurrencySymbol());
			$currency = Core_Model_Language::getCurrentCurrency()->getShortName();
			$fidelity_rate = $application->getFidelityRate();
			$last_order = (new Mcommerce_Model_Order())->findAll(['mcommerce_id' => $store->getMcommerceId(), 'store_id' => $store->getStoreId()], 'order_id DESC', ['limit' => 1])->current();
			$last_number = 0;
			if($last_order AND $last_order->getId()) {
				$last_number = intval(preg_replace('/[^0-9]/', '', $last_order->getNumber()));
			}
			$metadatas = $customer->getMetadatas();
			$order_num = 'O'.str_pad(++$last_number, 7, 0, STR_PAD_LEFT);				

			//$pwd = $methodInstance->getPwd();
			//$rest_id = $methodInstance->getRestId();
			//$shop_id = $methodInstance->getShopId();		

			$cart = $this->getCart();
			$errors = $cart->check();
			$paymentMethod = $cart->getPaymentMethod();
			$statusId = Mcommerce_Model_Order::DEFAULT_STATUS;
			
			// Keep a log of the promo and code if used!
			$promo = $this->getPromo();
			$cart = $this->getCart();
			$cart->setCustomerUUID($params['customer_uuid']);
			
			if ($promo) {
				$log = Mcommerce_Model_Promo_Log::createInstance($promo, $cart);
				$log->save();

				// Use points if needed!
				if ($promo->getPoints() && $cart->getCustomerId()) {
					$points = $promo->getPoints();
					$customer = (new Customer_Model_Customer())
						->find($cart->getCustomerId());
					// Decrease points!
					if ($customer->getId()) {
						$customerPoints = $customer->getMetaData('fidelity_points', 'points') * 1;
						$customerPoints = $customerPoints - $points;
						$customer->setMetadata('fidelity_points', 'points', $customerPoints)->save();
					}
				}
			}			
			
			
			$order = new Mcommerce_Model_Order();
			$order
				->fromCart($cart)
				->setStatusId($statusId);

			//replace metadatas
			$customer1 = (new Customer_Model_Customer())->find($cart->getCustomerId());
			if ($customer1->getId()) {
				$order->setOrderMetadatas(json_encode($customer1->getMetadatas(),JSON_UNESCAPED_UNICODE));
			}
			

			array_key_exists('notes', $params) ? $order->setNotes($params['notes'].$payment_url) : $order->setNotes($payment_url);
			$order->save();

			
			$order->sendToCustomer();
			$order->sendToStore();

				
			
			//Данные для кошелька
			//Настройки для успешного заказа
			$application_name= $methodInstance->getTitle();
			$description = "";

			foreach($cart->getLines() as $line) {
				$product = $line->getProduct();
				$description .=$line->getQty() . " x " . $line->getName() ."\n";
			
			}
			
			
			$json_execute_complete = array();
			$json_execute_complete['model'] = "Walletmcommerce_Model_Order";
			$json_execute_complete['args'] = array($order->getId(),$methodInstance->getStatusAfterPay());


			//Для отмены заказа
			$json_execute_cancel = array();
			$json_execute_cancel['model'] = "Walletmcommerce_Model_Order";
			$json_execute_cancel['args'] = array($order->getId(),Mcommerce_Model_Order::CANCEL_STATUS);


			//Подключаемся к кощельку
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
			$data['wallet_option_id']=$option_id;
			$wallet = (new Wallet_Model_Wallet())->find(["value_id" => $value_id]);
			$wallet_customer = $wallet->getCustomer($cart->getCustomerId());
			$t = $wallet_customer->createBill($cart->getTotal(),__("Payment order N") . $order->getNumber(),$description,$application_name,json_encode($json_execute_complete),json_encode($json_execute_cancel),$methodInstance->getCompleteText(),$methodInstance->getCancelText());
			
			//Теперь узнаем опцию карта
			$stmt = $db->query(
				'SELECT * FROM application_option WHERE code = ?',
				array('m_commerce')
			);
			$mobilcart_option_id = $stmt->fetch()['option_id'];
			$stmt = $db->query(
				'SELECT * FROM application_option_value WHERE option_id = ? and app_id = ?',
				array($mobilcart_option_id, $app_id)
			);
			$mobilcart_value_id = $stmt->fetch()['value_id'];			
			
			
			
			$data['wallet_bill_id']=$t;
			$data['valueId']=$valueId;
			$data['debug']=date("YmdHis");
			$data['intro']=$methodInstance->getIntroText() ;
			$data['wallet_customer_id']=$wallet_customer->getId() ;
			$data['wallet_id']=$wallet->getId() ;
			$data['wallet_value_id']=$wallet->getValueId();
			$data['wallet_option_id']=$option_id;
			$data['wallet_customer_score']=(float)$wallet_customer->getScore();
			$data['mobilcart_value_id']=$mobilcart_value_id;
			$data['bill_summ']=$cart->getTotal();

			
			//Очистим карту
			$this->getSession()->unsetCart();		
		} catch (Exception $e) {
                $data = [
                    'error' => 1,
                    'message' => $e->getMessage()
                ];		
			$this->_sendHtml($data);

        }	
		$this->_sendHtml($data);	
	}


	
	public function confirmorderAction() {
   
		$data['debug']=date("YmdHis");
		try {
            $request = $this->getRequest();
            if ($params = $request->getRawBody()) {
                $params = Nwicode_Json::decode($params);
            } else if ($data = $request->getPost()) {
                $params = $request->getPost();
            } else {
                $params = $request->getFilteredParams();
            }

			$wallet_customer = (new Wallet_Model_Customer())->find($params['wallet_customer_id']);
			$data['bill_status']='pending';
			if ($wallet_customer->getId()) {
				$bill = $wallet_customer->acceptBill($params['wallet_bill_id']);
				$data['bill_status']=$bill->getStatus();
			}
			
			$data['debug']=$params;

			
		} catch (Exception $e) {
                $data = [
                    'error' => 1,
                    'message' => $e->getMessage()
                ];		
			$this->_sendHtml($data);
            //$this->_redirect('mmobilcart/mobile_sales_error/index');
        }	
		$this->_sendHtml($data);
	}
}