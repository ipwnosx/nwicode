<?php
class Wallet_AdminController extends Admin_Controller_Default
{

    public function panelAction()
    {
        $this->loadPartials();
		
    }
	
    public function dashboardAction()
    {

        $this->getLayout()->setBaseRender('content', 'wallet/admin/dashboard.phtml', 'admin_view_default');
        $html = ['html' => $this->getLayout()->render()];
        $this->getLayout()->setHtml(Zend_Json::encode($html));
    }
	
    public function paymentAction()
    {
		$parameter = $this->getRequest()->getParams('parameter');
		Zend_Layout::getMvcInstance()->assign('params', $parameter);
        $this->getLayout()->setBaseRender('content', 'wallet/admin/payment.phtml', 'admin_view_default');
        $html = ['html' => $this->getLayout()->render()];
        $this->getLayout()->setHtml(Zend_Json::encode($html));
    }	
	
    public function settingsAction()
    {
	
        $this->getLayout()->setBaseRender('content', 'wallet/admin/settings.phtml', 'admin_view_default');
        $html = ['html' => $this->getLayout()->render()];
        $this->getLayout()->setHtml(Zend_Json::encode($html));
    }

	
    public function requestsAction()
    {
	
        $this->getLayout()->setBaseRender('content', 'wallet/admin/requests.phtml', 'admin_view_default');
        $html = ['html' => $this->getLayout()->render()];
        $this->getLayout()->setHtml(Zend_Json::encode($html));
    }
	
    public function billsAction()
    {
	
        $this->getLayout()->setBaseRender('content', 'wallet/admin/bills.phtml', 'admin_view_default');
        $html = ['html' => $this->getLayout()->render()];
        $this->getLayout()->setHtml(Zend_Json::encode($html));
    }	
    public function customersAction()
    {
	
        $this->getLayout()->setBaseRender('content', 'wallet/admin/customers.phtml', 'admin_view_default');
        $html = ['html' => $this->getLayout()->render()];
        $this->getLayout()->setHtml(Zend_Json::encode($html));
    }
	
    public function transactionsAction()
    {
	
        $this->getLayout()->setBaseRender('content', 'wallet/admin/transactions.phtml', 'admin_view_default');
        $html = ['html' => $this->getLayout()->render()];
        $this->getLayout()->setHtml(Zend_Json::encode($html));
    }
	
    public function addfundshistoryAction()
    {
	
        $this->getLayout()->setBaseRender('content', 'wallet/admin/addfundshistory.phtml', 'admin_view_default');
        $html = ['html' => $this->getLayout()->render()];
        $this->getLayout()->setHtml(Zend_Json::encode($html));
    }
	
	/*обновление настроек*/
	public function settingseditpostAction()
	{
		if($datas = $this->getRequest()->getPost()) {
            try {
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
				
				
				//Основное
				if ($datas['can_request']=='on') $wallet->setCanRequest(1); else $wallet->setCanRequest(0);
				if ($datas['can_upload']=='on') $wallet->setCanUpload(1); else $wallet->setCanUpload(0);
				if ($datas['can_transfer']=='on') $wallet->setCanTransfer(1); else $wallet->setCanTransfer(0);
				$wallet->setSignUpBonus($datas['sign_up_bonus']);
				$wallet->setData("c2c_commission",$datas['c2c_commission']);
				$wallet->setData("upload_commission",$datas['upload_commission']);
				
				
				$wallet->save();
				
				//Методы
				if ($datas['payout_methods']) {
					foreach ($datas['payout_methods'] as $payout_method_id=>$pm) {
						$method = new Wallet_Model_PayoutMethods();
						if (strpos($payout_method_id, 'new_')!== false && $pm['deleted']=='0') {
							$method
								->setTitle($pm['title'])
								->setMinimum($pm['minimum'])
								->setActive($pm['active'])
								->setDescription($pm['description'])
								->setWalletId($wallet->getId())
								->save();
						} else {
							$method->find($payout_method_id);
							if ($method->getId()) {
								$method
									->setTitle($pm['title'])
									->setMinimum($pm['minimum'])
									->setActive($pm['active'])
									->setDescription($pm['description'])
									->save();
									if ($pm['deleted']=='1') $method->delete();
							}
						}
					}
				}
                $html = [
                    'success' => '1',
                    'success_message' => __('Settings successfully saved'),
                    'message_timeout' => 2,
                    'message_button' => 0,
                    'message_loader' => 0
                ];
	
	
            }
            catch(Exception $e) {
                $html = [
                    'error' => 1,
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }	
            $this->_sendHtml($html);

        }
	}
	
	
	
    public function customerAction()
    {
		//error_reporting(E_ALL);
		//ini_set('display_errors', 1);	
        if ($customer_id = $this->getRequest()->getParam('customer_id')) {
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
			
			$customer = (new Customer_Model_Customer())->find($customer_id);
			
			Zend_Layout::getMvcInstance()->assign('current_customer', $customer->getId());
			
			$this->getLayout()->setBaseRender('content', 'wallet/admin/customer.phtml', 'admin_view_default');
			$html = ['html' => $this->getLayout()->render()];
			$this->getLayout()->setHtml(Zend_Json::encode($html));
		} else {
			$html = ['html' => print_r($this->getRequest()->getParams('customer_id'),true)];
			$this->getLayout()->setHtml(Zend_Json::encode($html));		
		}
    }

	/* обновление суммы на кошельке ручное*/
    public function customereditpostAction() {

        if($datas = $this->getRequest()->getPost()) {

            try {
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

				$errors = array();
				
				$customer = $wallet->getCustomer($datas['customer_id']);
				if (!$customer->getid()) {
					throw new Exception(__('An error occurred while saving. Please try again later.'));

				} else {
					//Проверки
					if (!is_numeric( $datas['amount'])) throw new Exception(__("Invalid amount"));
					if (is_numeric( $datas['amount']) and ($datas['amount'])<0) throw new Exception(__("Invalid amount"));
					
					
					
					//$customer->setScore($datas['amount'])->save();
					if ($datas['comment']=="") $datas['comment'] = __("Amount changed manually.");
					if (($datas['amount'] - $customer->getScore())>0) $type = "in"; else $type = "out";
					$datas['amount'] = $datas['amount'] - $customer->getScore();
					$customer->addTransaction($datas['amount'],$datas['comment'],$type);
				}
				
				
                $html = [
                    'success' => '1',
                    'customer_id' => $customer->getCustomerId(),

					"customer_balance" => $customer->getScore(),
                    'success_message' => __('Data successfully saved'),
                    'message_timeout' => 2,
                    'message_button' => 0,
                    'message_loader' => 0
                ];

            }
            catch(Exception $e) {
                $html = [
                    'error' => 1,
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->_sendHtml($html);

        }

    }    
	
	/*переброс баланса*/
	public function transferfundsAction() {
        if($datas = $this->getRequest()->getPost()) {

            try {
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
				$customer = $wallet->getCustomer($datas['customer_id']);
				$to_customer = (new Wallet_Model_Customer())->find($datas['to_customer_id']);	//у него внутренний ид
				
				if (!$customer->getid()) {
					throw new Exception(__('An error occurred while saving. Please try again later.'));

				} if (!$to_customer->getid()) {
					throw new Exception(__('An error occurred while saving. Please try again later.'));

				} else {
					//Проверки
					if (!is_numeric( $datas['amount'])) throw new Exception(__("Invalid amount"));
					if (is_numeric( $datas['amount']) and ($datas['amount'])<0) throw new Exception(__("Invalid amount"));
					if (is_numeric( $datas['amount']) and ($datas['amount'])>$customer->getScore()) throw new Exception(__("Invalid amount"));
					
					$amount = $datas['amount'] * (-1);
					$comment = $datas['comment'];
					$t = $customer->addTransaction($amount,$comment,"out",$customer->getId(),$to_customer->getId());
					
					//Вычтем комиссию, если есть
					$comission = (float)$wallet->getData("c2c_commission");
					$comission_summ = 0;
					if ($comission>0) {
						$comission_summ = (abs($amount)/100)*$comission;
						$amount = $amount+$comission_summ;	//потому что тут знвчение с обртатным знакрм
					}
					
					$in_transaction_id = $to_customer->addTransaction($amount*(-1),$comment,"in",$customer->getId(),$to_customer->getId(),$t);
					
					//Запишем в транзакцию примечание с комиссией
					if ($comission>0) {
						$transaction = (new Wallet_Model_Transactions())
							->find($in_transaction_id)
							->setTransactionNote(__("Commission"). " (".$comission."%): ".round($comission_summ,2))
							->setData("comission_summ",$comission_summ)
							->save();
					}

				}
				
                $html = [
                    'success' => '1',
                    'customer_id' => $customer->getCustomerId(),

					"customer_balance" => $customer->getScore(),
					"customer2_balance" => $to_customer->getScore(),
					"customer2_id" => $to_customer->getCustomerId(),
                    'success_message' => __('Data successfully saved'),
                    'message_timeout' => 2,
                    'message_button' => 0,
                    'message_loader' => 0
                ];				
	
	
            }
            catch(Exception $e) {
                $html = [
                    'error' => 1,
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->_sendHtml($html);

        }	
	}
	
	//Создадим запрос на выплату
	public function requestpayoutAction() {
		if($datas = $this->getRequest()->getPost()) {
			try {
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
				$customer = $wallet->getCustomer($datas['customer_id']);
				
				
				if (!$customer->getid()) {
					throw new Exception(__('An error occurred while saving. Please try again later.'));

				} else {

					//Проверки
					if (!is_numeric( $datas['amount'])) throw new Exception(__("Invalid amount"));
					if (is_numeric( $datas['amount']) and ($datas['amount'])<0) throw new Exception(__("Invalid amount"));				
				
					//Получим метод оплаты
					$payout_method = (new Wallet_Model_PayoutMethods())->find($datas['payout_method']);
					
					if (is_numeric( $datas['amount']) and ($datas['amount'])<$payout_method->getMinimum()) throw new Exception(__("Invalid amount"));			
					if (is_numeric( $datas['amount']) and ($datas['amount'])>$customer->getScore()) throw new Exception(__("Invalid amount"));
					
					//Добавим транзакцию
					$comment = trim(__("Request payout") . ": " . $payout_method->getTitle() . " " .$datas['comment']);
					$amount = $datas['amount'];
					$t = $customer->addTransaction($amount*(-1),$comment,"out");
					
					//Добавим выплату
					$request_id = $customer->addPayoutRequest($amount,$comment,$t, $payout_method->getId());

				}				
				

				
			
				$html = [
					'success' => '1',
					'customer_id' => $customer->getCustomerId(),
					'datas' => $datas,
					"customer_balance" => $customer->getScore(),
					"request_id" => $request_id,
					'success_message' => __('Data successfully saved'),
					'message_timeout' => 2,
					'message_button' => 0,
					'message_loader' => 0
				];			
			
			
            }
            catch(Exception $e) {
                $html = [
                    'error' => 1,
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->_sendHtml($html);	
		}
	}
	
	/*блокировка кошельька*/
	public function customerblockpostAction() {

        if($datas = $this->getRequest()->getPost()) {

            try {
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

				$errors = array();
				
				$customer = $wallet->getCustomer($datas['customer_id']);
				if (!$customer->getid()) {
					throw new Exception(__('An error occurred while saving. Please try again later.'));

				} else {
					if ($datas['block']=="1") $customer->setIsBlocked(1);
					if ($datas['block']=="0") $customer->setIsBlocked(0);
					$customer->save();
					
					if ($datas['block']=="1") $customer->addTransaction(0,__('Wallet blocked'),"in");
					if ($datas['block']=="0") $customer->addTransaction(0,__('Wallet unblocked'),"in");				
				}
				
				
                $html = [
                    'success' => '1',
                    'customer_id' => $customer->getCustomerId(),
					"customer_blocked" => ($customer->getIsBlocked()=="1")?("1"):("0"),
                    'success_message' => __('Data successfully saved'),
                    'message_timeout' => 2,
                    'message_button' => 0,
                    'message_loader' => 0
                ];

            }
            catch(Exception $e) {
                $html = [
                    'error' => 1,
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->_sendHtml($html);

        }

    }
	
	
	
	/*Список транзакций кошелька*/
	public function customertransactionsAction() {
		if($datas = $this->getRequest()->getPost()) {
            try {	
                 
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

				$errors = array();
				$transactions = array();
				$customer = $wallet->getCustomer($datas['customer_id']);

				if ($customer->getTransactions()) {
					foreach($customer->getTransactions() as $tr) {
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
						
						$transactions[] = array($tr->getCreatedAt(),$tr->getSumm(),(($tr->getOperationSumm()>0)?"+":"").$tr->getOperationSumm(),nl2br($tr->getDescription() . "\n" .$tr->getTransactionNote()),$account);
					
					}
				
				}
				
				 $html = [
                    'success' => '1',
                    'transactions' => $transactions
                ];           
			}
            catch(Exception $e) {
                $html = [
                    'error' => 1,
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->_sendHtml($html);
		}
	}
	
	
	/*Список выплат с кошелька*/
	public function customerpayoutrequestsAction() {
		if($datas = $this->getRequest()->getPost()) {
            try {	
                 
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

				$errors = array();
				$requests = array();
				$customer = $wallet->getCustomer($datas['customer_id']);

				if ($customer->getPayoutRequests()) {
					foreach($customer->getPayoutRequests() as $tr) {

					
						$p = array();
						$p[]=$tr->getCreatedAt();
						$p[]=$tr->getPayoutMethodTitle();
						$p[]=$tr->getSumm();
						$p[]=nl2br($tr->getCustomerInfo());
						$p[]=$tr->getStatus();
						$p[]=nl2br($tr->getAdminInfo());
						if ($tr->getStatus()=="pending") {
							$p[]="<button type='button' class='btn btn-danger' onclick='cancel_request_payout(".$tr->getId().");'><i class='fa fa-ban' aria-hidden='true'></i></button>";
						} else {
							$p[]="";
						}
						
						
						$requests[] = $p;
					
					}
				
				}
				
				 $html = [
                    'success' => '1',
                    'requests' => $requests
                ];           
			}
            catch(Exception $e) {
                $html = [
                    'error' => 1,
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->_sendHtml($html);
		}
	}


	//Отменим запрос на выплату
	public function cancelpayoutAction() {
		if($datas = $this->getRequest()->getPost()) {
			try {
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
				$customer = $wallet->getCustomer($datas['customer_id']);
				
				if (!$customer->getid()) {
					throw new Exception(__('An error occurred while saving. Please try again later.'));

				} else {
					$customer->cancelPayout($datas['wallet_payout_request_id']);

				}				

			
				$html = [
					'success' => '1',
					'customer_id' => $customer->getCustomerId(),
					'datas' => $datas,
					"customer_balance" => $customer->getScore(),
					'success_message' => __('Data successfully saved'),
					'message_timeout' => 2,
					'message_button' => 0,
					'message_loader' => 0
				];			
			
			
            }
            catch(Exception $e) {
                $html = [
                    'error' => 1,
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->_sendHtml($html);	
		}
	}
	
	//Информация о выплате
    public function requestAction()
    {
		//error_reporting(E_ALL);
		//ini_set('display_errors', 1);	
        if ($wallet_payout_request_id = $this->getRequest()->getParam('wallet_payout_request_id')) {
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
			
			
			Zend_Layout::getMvcInstance()->assign('wallet_payout_request_id', $wallet_payout_request_id);
			
			$this->getLayout()->setBaseRender('content', 'wallet/admin/request.phtml', 'admin_view_default');
			$html = ['html' => $this->getLayout()->render()];
			$this->getLayout()->setHtml(Zend_Json::encode($html));
		} else {
			$html = ['html' => print_r($this->getRequest()->getParams('wallet_payout_request_id'),true)];
			$this->getLayout()->setHtml(Zend_Json::encode($html));		
		}
    }
	
	//Редактирование выплаты
	public function requesteditAction() {
	
		if($datas = $this->getRequest()->getPost()) {
			try {
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
				
				
				$pr = new Wallet_Model_PayoutRequest();
				$pr->find($datas['wallet_payout_request_id']);
	
					if (!$pr->getid()) {
						throw new Exception(__('An error occurred while saving. Please try again later.'));

					} else {
						$pr
							->setAdminInfo($datas['admin_info'])
							->save();
							
						if ($pr->getStatus()=='pending') {
							//Отмена
							if ($datas['status']=='canсel' || $datas['status']=='decline') {
								$pr->getCustomer()->cancelPayout($datas['wallet_payout_request_id']);
								$pr->setStatus($datas['status'])->save();
							}
							//Отмена
							if ($datas['status']=='complete') {
								$pr->setStatus($datas['status'])->setApprovedAt(Zend_Date::now()->toString('YYYY-MM-dd HH:mm:ss'))->save();
							}							
						
						}


					}				

			
				$html = [
					'success' => '1',
					'datas' => $datas,
					"wallet_payout_request_id" => $datas['wallet_payout_request_id'],
					"status" => $pr->getStatus(),
					"admin_info" => $pr->getAdminInfo(),
					'success_message' => __('Data successfully saved'),
					'message_timeout' => 2,
					'message_button' => 0,
					'message_loader' => 0
				];			
			
			
            }
            catch(Exception $e) {
                $html = [
                    'error' => 1,
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->_sendHtml($html);	
		}
		
	}
	
	//Создание счета
	public function createbillAction() {
		
		if($datas = $this->getRequest()->getPost()) {
			try {
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
				$customer = $wallet->getCustomer($datas['customer_id']);
				
				//Проверки
				if (!is_numeric( $datas['amount'])) throw new Exception(__("Invalid amount"));
				if (is_numeric( $datas['amount']) and ($datas['amount'])<0) throw new Exception(__("Invalid amount"));	
				if (is_numeric( $datas['amount']) and ($datas['amount'])>$customer->getScore()) throw new Exception(__("Invalid amount"));
				if (!$datas['title'] || $datas['title']=="") throw new Exception(__("Bill title is required!"));
			
			
				//Создаем счет
				$t = $customer->createBill($datas['amount'],$datas['title'],$datas['description'],$bill_source = __("Application Administrator Account"));
				$html = [
					'success' => '1',
					'datas' => $datas,
					"wallet_bill_id" => $t,
					'success_message' => __('Data successfully saved'),
					'message_timeout' => 2,
					'message_button' => 0,
					'message_loader' => 0
				];				
			}
            catch(Exception $e) {
                $html = [
                    'error' => 1,
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->_sendHtml($html);	
		}	
	}
	
	
	/*Список выплат с кошелька*/
	public function customerbillsAction() {
		if($datas = $this->getRequest()->getPost()) {
            try {	
                 
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

				$errors = array();
				$bills = array();
				$customer = $wallet->getCustomer($datas['customer_id']);

				if ($customer->getBills()) {
					foreach($customer->getBills() as $tr) {

						
						$p = array();
						$p[]=$tr->getCreatedAt();
						$p[]=$tr->getSumm();
						$p[]=$tr->getBillSource();
						$p[]=$tr->getTitle();
						$p[]=nl2br($tr->getDescription());
						$p[]=$tr->getStatus();
						if ($tr->getStatus()=="pending") {
							$p[]="<div class='btn-group' role='group'><button type='button' class='btn btn-danger' onclick='cancel_bill(".$tr->getId().");'><i class='fa fa-ban' aria-hidden='true'></i></button> <button type='button' class='btn btn-primary' onclick='accept_bill(".$tr->getId().");'><i class='fa ion-checkmark-round' aria-hidden='true'></i></button></div>";
						} else {
							$p[]="";
						}
						
						
						$bills[] = $p;
					
					}
				
				}
				
				 $html = [
                    'success' => '1',
                    'bills' => $bills
                ];           
			}
            catch(Exception $e) {
                $html = [
                    'error' => 1,
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->_sendHtml($html);
		}
	}

	
	/*Список пополнений с кошелька*/
	public function customeraddfundshistoryAction() {
		if($datas = $this->getRequest()->getPost()) {
            try {		
	
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
				
				$errors = array();
				$history = array();
				$customer = $wallet->getCustomer($datas['customer_id']);				
	
				if ($customer->getAddFundsHistory()) {
					foreach($customer->getAddFundsHistory() as $tr) {

						
						$p = array();
						$p[]=$tr->getCreatedAt();
						$p[]=$tr->getSumm();
						$p[]=$tr->getCode();
						
						if ($tr->getComplete()=="-1") $p[]=__("Canceled");
						else if ($tr->getComplete()=="0") $p[]=__("Pending");
						else if ($tr->getComplete()=="1") $p[]=__("OK");
						
						
						$history[] = $p;
					
					}
				
				}
				
				 $html = [
                    'success' => '1',
                    'history' => $history
                ];	
	
			}
            catch(Exception $e) {
                $html = [
                    'error' => 1,
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->_sendHtml($html);
		}	
	}
	
	//Проведение счета
	public function acceptbillAction() {
		
		if($datas = $this->getRequest()->getPost()) {
			try {
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
				$customer = $wallet->getCustomer($datas['customer_id']);
				
				$bill = new Wallet_Model_Bill();
				$bill->find($datas['bill_id']);
				if ($customer->getScore()<$bill->getSumm()) throw new Exception(__("Invalid amount"));
				
				//Создаем счет
				$t = $customer->acceptBill($datas['bill_id']);
				$html = [
					'success' => '1',
					'datas' => $datas,
					'status'=>$t->getStatus(),
					'debug'=>$bill->getData(),
					'debug1'=>$t->getData(),
					"customer_balance" => $customer->getScore(),
					'success_message' => __('Data successfully saved'),
					'message_timeout' => 2,
					'message_button' => 0,
					'message_loader' => 0
				];				
			}
            catch(Exception $e) {
                $html = [
                    'error' => 1,
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->_sendHtml($html);	
		}	
	}


	//отмена счета
	public function cancelbillAction() {
		
		if($datas = $this->getRequest()->getPost()) {
			try {
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
				$customer = $wallet->getCustomer($datas['customer_id']);
				
				//Создаем счет
				$t = $customer->cancelBill($datas['bill_id']);
				$html = [
					'success' => '1',
					'datas' => $datas,
					"customer_balance" => $customer->getScore(),
					'status'=>$t->getStatus(),
					'debug1'=>$t->getData(),
					'success_message' => __('Data successfully saved'),
					'message_timeout' => 2,
					'message_button' => 0,
					'message_loader' => 0
				];				
			}
            catch(Exception $e) {
                $html = [
                    'error' => 1,
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->_sendHtml($html);	
		}	
	}

}