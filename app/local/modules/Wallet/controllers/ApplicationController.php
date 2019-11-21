<?php

class Wallet_ApplicationController extends Application_Controller_Default
{

     public function viewAction() {
        $this->loadPartials();
    }

    public function loadAction(){
        $payload = [
            'title' => __('Wallset'),
            'icon' => 'fa-cogs',
        ];

        $this->_sendJson($payload);
    }

    public function editpostAction()
    {
        $request = $this->getRequest();
        $values = $request->getPost();

        try {


			$wallet = new Wallet_Model_Wallet();

			$wallet->setValueId($values['value_id']);


			$wallet->save();

			/*create customers*/
			
			//Не будем создавать пользователей - они создадутся автоматически при первов входе
			
			/*$application = $this->getApplication();
			$customers = $application->getCustomers();
			foreach ($customers as $customer) {
				$wallet_customer = new Wallet_Model_Customer();
				$wallet_customer				
					->setCustomerId($customer->getId())
					->setWalletId($wallet->getId())
					->setScore($wallet->getSignUpBonus())
					->setIsBlocked(0)
					->save();
			}*/



			$payload = [
                  'success' => '1',
                    'success_message' => $this->_('Info successfully saved'),
                    'message_timeout' => 2,
                    'message_button' => 0,
                    'message_loader' => 0
			];

        } catch (Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }



}