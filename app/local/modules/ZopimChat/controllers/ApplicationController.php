<?php

class ZopimChat_ApplicationController extends Application_Controller_Default
{

    public function viewAction() {
        $this->loadPartials();
    }

    public function loadAction(){
        $payload = [
            'title' => __('Testlic'),
            'icon' => 'fa-cogs',
        ];

        $this->_sendJson($payload);
    }

    public function editpostAction()
    {
        $request = $this->getRequest();
        $values = $request->getPost();

        try {

			$optionValue = $this->getCurrentOptionValue();

			$zopim_chat = (new ZopimChat_Model_ZopimChat())
				->find($values['value_id'], 'value_id');

			$zopim_chat->setData($values);


			$zopim_chat->save();



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