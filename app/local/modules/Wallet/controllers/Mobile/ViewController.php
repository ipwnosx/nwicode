<?php

class Wallet_Mobile_ViewController extends Application_Controller_Mobile_Default {


    public function findAction() {

        if($value_id = $this->getRequest()->getParam('value_id')) {

            try {
			
				$option = $this->getCurrentOptionValue();
				$wallet = (new Wallet_Model_Wallet())->find(["value_id" => $value_id]);
				$customer = $this->getSession()->getCustomer();

				//Если пользователя нет, то создадим
				if ($customer) {
					$wallet_customer = $wallet->getCustomer($customer->getId());
					if (!$wallet_customer->getId()) $wallet_customer = $wallet->createCustomer($customer->getId());
				} else {
					$wallet_customer = new Wallet_Model_Customer();	//пустышка
				}
				
				//методы
				$payment_methods = array();
				foreach ($wallet->getPayoutMethods() as $pm) {
					$p = $pm->getData();
					$p['comment']="";	//это поле для объекта в заказе выплаты через приложение
					$p['amount']="";	//это поле для объекта в заказе выплаты через приложение
					$payment_methods[]=$p;
				}
		
				//Количество неоплаченных счетов
				$bills = $wallet_customer->getBills();
				$bills_pending = 0;
				if ($bills) foreach($bills as $bill) if ($bill->getStatus()=="pending") $bills_pending++;
                $data = array(
                    "wallet" => $wallet->getData(),
					"customer" =>$wallet_customer->getData(),
					"payment_methods" => $payment_methods,
					"bills_pending" => $bills_pending
                );
				
				$payment_systems = (new Wallet_Model_PaymentSystems())->findAll();
				$data['payments_systems']=array();
				foreach($payment_systems as $ps) {
					$p_model = $ps->getData('model');
					$model = new $p_model();
					$model->find(array('wallet_id'=>$wallet->getId()));
					if ($model->getId() && $model->getEnabled()==1) {
					
						//Ссылка для оплаты
						$url = parent::getPath($ps->getData('url'), array('value_id' => $this->getCurrentOptionValue()->getValueId(),'wallet_id'=>$wallet->getId(), 'wallet_customer_id' => $wallet_customer->getId()));
						$data['payments_systems'][]=array("title"=>$model->getTitle(),"code"=>$ps->getCode(),"type"=>$ps->getType(),"state_name"=>$ps->getStateName(),'url'=>$url);
					
					}
				}
				
            }
            catch(Exception $e) {
                $data = array('error' => 1, 'message' => $e->getMessage());
            }

        }else{
            $data = array('error' => 1, 'message' => 'An error occurred during process. Please try again later.');
        }

        $this->_sendHtml($data);

    }
    public function findtransactionsAction() {


        if($value_id = $this->getRequest()->getParam('value_id')) {

            try {
			
				$option = $this->getCurrentOptionValue();
				$wallet = (new Wallet_Model_Wallet())->find(["value_id" => $value_id]);
				$customer = $this->getSession()->getCustomer();

				//Если пользователя нет, то создадим
				$wallet_customer = $wallet->getCustomer($customer->getId());
				if (!$wallet_customer->getId()) $wallet_customer = $wallet->createCustomer($customer->getId());
			
				$transactions = array();
				if ($wallet_customer->getTransactions()) {
					foreach($wallet_customer->getTransactions() as $tr) {
						$account = "-";
						
						//Если откудато
						if ($tr->getType()=="in" && $tr->getFromCustomerId()!=0) {
							$from_account = (new Wallet_Model_Customer())->find($tr->getFromCustomerId());
							if ($from_account->getId()) $account = $from_account->getEmail();
						}
						
						if ($tr->getType()=="out" && $tr->getToCustomerId()!=0) {
							$to_account = (new Wallet_Model_Customer())->find($tr->getToCustomerId());
							if ($to_account->getId()) $account = $to_account->getEmail();
						}
						
						$transactions[] = array("date"=>$tr->getCreatedAt(),"summ"=>$tr->getSumm(),"operation_summ"=>(($tr->getOperationSumm()>0)?"+":"").$tr->getOperationSumm(),"description"=>$tr->getDescription(),"from_account"=>$account,"transaction_note"=>$tr->getTransactionNote(),);
					
					}
					$transactions = array_reverse($transactions);
				}
                $data = array(
                    "wallet" => $wallet->getData(),
                    "customer" => $wallet_customer->getData(),
					"transactions" =>$transactions
                );				
            }
            catch(Exception $e) {
                $data = array('error' => 1, 'message' => $e->getMessage());
            }

        }else{
            $data = array('error' => 1, 'message' => 'An error occurred during process. Please try again later.');
        }

        $this->_sendHtml($data);

    }
	
	
    public function findpayoutsAction() {


        if($value_id = $this->getRequest()->getParam('value_id')) {

            try {
			
				$option = $this->getCurrentOptionValue();
				$wallet = (new Wallet_Model_Wallet())->find(["value_id" => $value_id]);
				$customer = $this->getSession()->getCustomer();

				//Если пользователя нет, то создадим
				$wallet_customer = $wallet->getCustomer($customer->getId());
				if (!$wallet_customer->getId()) $wallet_customer = $wallet->createCustomer($customer->getId());
			
				$requests = array();


				if ($wallet_customer->getPayoutRequests()) {
					foreach($wallet_customer->getPayoutRequests() as $tr) {

					
						$p = array();
						$p['id']=$tr->getId();
						$p['date']=$tr->getCreatedAt();
						$p['method']=$tr->getPayoutMethodTitle();
						$p['amount']=$tr->getSumm();
						$p['info']=$tr->getCustomerInfo();
						$p['status']=$tr->getStatus();
						$p['reply']=$tr->getAdminInfo();
						
						$requests[] = $p;
					
					}
					$requests = array_reverse($requests);
				}
                $data = array(
                    "wallet" => $wallet->getData(),
                    "customer" => $wallet_customer->getData(),
					"requests" =>$requests
                );				
            }
            catch(Exception $e) {
                $data = array('error' => 1, 'message' => $e->getMessage());
            }

        }else{
            $data = array('error' => 1, 'message' => 'An error occurred during process. Please try again later.');
        }

        $this->_sendHtml($data);

    }
	
	public function cancelpayoutAction() {


        if($value_id = $this->getRequest()->getParam('value_id')) {

            try {
			
				$option = $this->getCurrentOptionValue();
				$wallet = (new Wallet_Model_Wallet())->find(["value_id" => $value_id]);
				$wallet_payout_request_id = $this->getRequest()->getParam('wallet_payout_request_id');
				$wpr = new Wallet_Model_PayoutRequest();
				$wpr->find($wallet_payout_request_id);
				if (!$wpr->getId()) {
					throw new Exception(__('An error occurred while saving. Please try again later.'));
				} else {
					
					//Удалим и повторим кусок списка
					$customer = $this->getSession()->getCustomer();

					//Если пользователя нет, то создадим
					$wallet_customer = $wallet->getCustomer($customer->getId());
					if (!$wallet_customer->getId()) $wallet_customer = $wallet->createCustomer($customer->getId());
					$wallet_customer->cancelPayout($wallet_payout_request_id);
					$requests = array();


					if ($wallet_customer->getPayoutRequests()) {
						foreach($wallet_customer->getPayoutRequests() as $tr) {

						
							$p = array();
							$p['id']=$tr->getId();
							$p['date']=$tr->getCreatedAt();
							$p['method']=$tr->getPayoutMethodTitle();
							$p['amount']=$tr->getSumm();
							$p['info']=$tr->getCustomerInfo();
							$p['status']=$tr->getStatus();
							$p['reply']=$tr->getAdminInfo();
							
							$requests[] = $p;
						
						}
						$requests = array_reverse($requests);
					}
					$data = array(
						"wallet" => $wallet->getData(),
						"customer" => $wallet_customer->getData(),
						"requests" =>$requests
					);					
				}
				
            }
            catch(Exception $e) {
                $data = array('error' => 1, 'message' => $e->getMessage());
            }

        }else{
            $data = array('error' => 1, 'message' => 'An error occurred during process. Please try again later.');
        }

        $this->_sendHtml($data);

    }	
	
	
	public function makerequestAction() {


        if($value_id = $this->getRequest()->getParam('value_id')) {
			try {
				$datas = Zend_Json::decode($this->getRequest()->getRawBody());
				$wallet = (new Wallet_Model_Wallet())->find(["value_id" => $value_id]);
				$customer = $this->getSession()->getCustomer();
				$wallet_customer = $wallet->getCustomer($customer->getId());
				if (!$wallet_customer->getId()) $wallet_customer = $wallet->createCustomer($customer->getId());
			
				//Проверки
				if (!isset($datas['wallet_payout_methods_id'])) throw new Exception(__("Invalid method"));
				
				$payout_method = (new Wallet_Model_PayoutMethods())->find($datas['wallet_payout_methods_id']);
				if (!$payout_method->getId()) throw new Exception(__("Invalid method"));
				
				if (!is_numeric( $datas['summ'])) throw new Exception(__("Invalid amount"));
				if (is_numeric( $datas['summ']) and ($datas['summ'])<0) throw new Exception(__("Invalid amount"));	
				if (is_numeric( $datas['summ']) and ($datas['summ'])<$payout_method->getMinimum()) throw new Exception(__("Invalid amount"));	
				if (is_numeric( $datas['summ']) and ($datas['summ'])>$wallet_customer->getScore()) throw new Exception(__("Invalid amount"));	
				
				//Добавим транзакцию
				$comment = trim(__("Request payout") . ": " . $payout_method->getTitle() . " " .$datas['comment']);
				$amount = $datas['summ'];
				$t = $wallet_customer->addTransaction($amount*(-1),$comment,"out");
				
				//Добавим выплату
				$request_id = $wallet_customer->addPayoutRequest($amount,$comment,$t, $payout_method->getId());				
				
				$data = array();
				$data['datas'] = $datas;
				$data['value_id'] = $value_id;
				$data['request_id'] = $request_id;
		
		
            }
            catch(Exception $e) {
                $data = array('error' => 1, 'message' => $e->getMessage());
            }		
        }else{
            $data = array('error' => 1, 'message' => 'An error occurred during process. Please try again later.');
        }

        $this->_sendHtml($data);

    }			

	
	public function maketransferAction() {


        if($value_id = $this->getRequest()->getParam('value_id')) {
			try {
				if ($this->getSession()->getAppId()) $app_id = $this->getSession()->getAppId(); else $app_id=$this->getApplication()->getId();
				$datas = Zend_Json::decode($this->getRequest()->getRawBody());
				$wallet = (new Wallet_Model_Wallet())->find(["value_id" => $value_id]);
				$customer = $this->getSession()->getCustomer();
				$wallet_customer = $wallet->getCustomer($customer->getId());
				if (!$wallet_customer->getId()) $wallet_customer = $wallet->createCustomer($customer->getId());
			
				//Проверки
				if (!isset($datas['to_account'])) throw new Exception(__("Recepient not found"));
				
				//Ищем получателя по почте
				$recepient = new Customer_Model_Customer();
				$recepient->find(array("app_id"=>$app_id,"email"=>$datas['to_account']));
				if (!$recepient->getId()) throw new Exception(__("Recepient not found"));
				$wallet_recepient = $wallet->getCustomer($recepient->getId());
				if (!$wallet_recepient->getId()) $wallet_recepient = $wallet->createCustomer($recepient->getId());				
				
				

				
				if (!is_numeric( $datas['summ'])) throw new Exception(__("Invalid amount"));
				if (is_numeric( $datas['summ']) and ($datas['summ'])<0) throw new Exception(__("Invalid amount"));	
				if (is_numeric( $datas['summ']) and ($datas['summ'])>$wallet_customer->getScore()) throw new Exception(__("Invalid amount"));	
				if ($wallet_recepient->getId()==$wallet_customer->getId()) throw new Exception(__("Recepient not found"));
				//Добавим транзакцию
				$comment = $datas['comment'];
				$amount = $datas['summ'] * (-1);
				$t = $wallet_customer->addTransaction($amount,$comment,"out",$wallet_customer->getId(),$wallet_recepient->getId());
				
				//Вычтем комиссию, если есть
				$comission = (float)$wallet->getData("c2c_commission");
				$comission_summ = 0;
				if ($comission>0) {
					$comission_summ = (abs($amount)/100)*$comission;
					$amount = $amount+$comission_summ;	//потому что тут знвчение с обртатным знакрм
				}				
				
				$in_transaction_id = $wallet_recepient->addTransaction($amount*(-1),$comment,"in",$wallet_customer->getId(),$wallet_recepient->getId(),$t);
				
				//Запишем в транзакцию примечание с комиссией
				if ($comission>0) {
					$transaction = (new Wallet_Model_Transactions())
						->find($in_transaction_id)
						->setTransactionNote(__("Commission"). " (".$comission."%): ".round($comission_summ,2))
						->setData("comission_summ",$comission_summ)
						->save();
				}	
				
				$data = array();
				$data['datas'] = $datas;
				$data['value_id'] = $value_id;
				$data['transaction_id'] = $t;
		
		
            }
            catch(Exception $e) {
                $data = array('error' => 1, 'message' => $e->getMessage());
            }		
        }else{
            $data = array('error' => 1, 'message' => 'An error occurred during process. Please try again later.');
        }

        $this->_sendHtml($data);

    }		

	
    public function findbillsAction() {


        if($value_id = $this->getRequest()->getParam('value_id')) {

            try {
			
				$option = $this->getCurrentOptionValue();
				$wallet = (new Wallet_Model_Wallet())->find(["value_id" => $value_id]);
				$customer = $this->getSession()->getCustomer();

				//Если пользователя нет, то создадим
				$wallet_customer = $wallet->getCustomer($customer->getId());
				if (!$wallet_customer->getId()) $wallet_customer = $wallet->createCustomer($customer->getId());
			
				$bills = array();


				if ($wallet_customer->getBills()) {
					foreach($wallet_customer->getBills() as $tr) {

					
						$p = array();
						$p['id']=$tr->getId();
						$p['date']=$tr->getCreatedAt();
						$p['bill_source']=$tr->getBillSource();
						$p['amount']=$tr->getSumm();
						$p['title']=$tr->getTitle();
						$p['description']=$tr->getDescription();
						$p['status']=$tr->getStatus();
						
						$bills[] = $p;
					
					}
					$bills = array_reverse($bills);
				}
                $data = array(
                    "wallet" => $wallet->getData(),
                    "customer" => $wallet_customer->getData(),
					"bills" =>$bills
                );				
            }
            catch(Exception $e) {
                $data = array('error' => 1, 'message' => $e->getMessage());
            }

        }else{
            $data = array('error' => 1, 'message' => 'An error occurred during process. Please try again later.');
        }

        $this->_sendHtml($data);

    }	
	
	public function findbillAction() {


        if($value_id = $this->getRequest()->getParam('value_id')) {
			$wallet_bill_id = $this->getRequest()->getParam('wallet_bill_id');
            try {
			
				$option = $this->getCurrentOptionValue();
				$wallet = (new Wallet_Model_Wallet())->find(["value_id" => $value_id]);
				$customer = $this->getSession()->getCustomer();

				//Если пользователя нет, то создадим
				$wallet_customer = $wallet->getCustomer($customer->getId());
				if (!$wallet_customer->getId()) $wallet_customer = $wallet->createCustomer($customer->getId());
			
				$bills = array();
				
				$bill = new Wallet_Model_Bill();
				$bill->find($wallet_bill_id);
				if (!$bill->getId()) throw new Exception(__('An error occurred while saving. Please try again later.'));
				$bill_array = $bill->getData();
				$bill_array['description'] = nl2br($bill_array['description']);
				$bill_array['summ'] = (float)$bill_array['summ'];
				$wallet_customer =$wallet_customer->getData();
				$wallet_customer['score'] = (float)$wallet_customer['score'];
				
                $data = array(
                    "wallet_bill_id" => $wallet_bill_id,
                    "wallet" => $wallet->getData(),
                    "customer" => $wallet_customer,
					"bill" =>$bill_array
                );				
            }
            catch(Exception $e) {
                $data = array('error' => 1, 'message' => $e->getMessage());
            }

        }else{
            $data = array('error' => 1, 'message' => 'An error occurred during process. Please try again later.');
        }

        $this->_sendHtml($data);

    }
	
	public function acceptbillAction() {


        if($value_id = $this->getRequest()->getParam('value_id')) {
			$wallet_bill_id = $this->getRequest()->getParam('wallet_bill_id');
            try {
			
				$option = $this->getCurrentOptionValue();
				$wallet = (new Wallet_Model_Wallet())->find(["value_id" => $value_id]);
				$customer = $this->getSession()->getCustomer();

				//Если пользователя нет, то создадим
				$wallet_customer = $wallet->getCustomer($customer->getId());
				if (!$wallet_customer->getId()) $wallet_customer = $wallet->createCustomer($customer->getId());
			
				$bills = array();
				
				$bill = new Wallet_Model_Bill();
				$bill->find($wallet_bill_id);
				if (!$bill->getId()) throw new Exception(__('An error occurred while saving. Please try again later.'));

				$t = $wallet_customer->acceptBill($wallet_bill_id);
				
                $data = array(
                    "wallet_bill_id" => $wallet_bill_id,
                    "wallet" => $wallet->getData(),
                    "customer" => $wallet_customer->getData(),
					//"t" =>$t->getData()
                );				
            }
            catch(Exception $e) {
                $data = array('error' => 1, 'message' => $e->getMessage());
            }

        }else{
            $data = array('error' => 1, 'message' => 'An error occurred during process. Please try again later.');
        }

        $this->_sendHtml($data);

    }

	public function cancelbillAction() {

        if($value_id = $this->getRequest()->getParam('value_id')) {
			$wallet_bill_id = $this->getRequest()->getParam('wallet_bill_id');
            try {
			
				$option = $this->getCurrentOptionValue();
				$wallet = (new Wallet_Model_Wallet())->find(["value_id" => $value_id]);
				$customer = $this->getSession()->getCustomer();

				//Если пользователя нет, то создадим
				$wallet_customer = $wallet->getCustomer($customer->getId());
				if (!$wallet_customer->getId()) $wallet_customer = $wallet->createCustomer($customer->getId());
			
				$bills = array();
				
				$bill = new Wallet_Model_Bill();
				$bill->find($wallet_bill_id);
				if (!$bill->getId()) throw new Exception(__('An error occurred while saving. Please try again later.'));

				$t = $wallet_customer->cancelBill($wallet_bill_id);
				
                $data = array(
                    "wallet_bill_id" => $wallet_bill_id,
                    "wallet" => $wallet->getData(),
                    "customer" => $wallet_customer->getData(),
					//"t" =>$t->getData()
                );				
            }
            catch(Exception $e) {
                $data = array('error' => 1, 'message' => $e->getMessage());
            }

        }else{
            $data = array('error' => 1, 'message' => 'An error occurred during process. Please try again later.');
        }

        $this->_sendHtml($data);
	
    }	
	
}