<?php

class Followus_ApplicationController extends Application_Controller_Default {

    public function viewAction() {
        $this->loadPartials();
    }

    public function loadAction(){
        $payload = [
            'title' => __('FollowUs'),
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

			$followus = (new Followus_Model_Followus())
				->find($values['value_id'], 'value_id');

			$followus->setData($values);


			$followus->save();

			//save links
			if ($values['link_new']) {
				foreach ($values['link_new'] as $new_line_id=>$new_line) if ($new_line_id!="new_item_pos") {
					$l = new Followus_Model_Followus_Line();
					$l->setFollowusId($followus->getId())
						->setValueId($values['value_id'])
						->setIcon($new_line['icon'])
						->setUrl($new_line['url'])
						->setTitle($new_line['title'])
						->save();
				}
			
			}
			
			//save edit links
			if ($values['link_edit']) {
				foreach ($values['link_edit'] as $edit_line_id=>$edit_line)  {
					$l = new Followus_Model_Followus_Line();
					$l->find($edit_line['followus_line_id']);
					if ($l->getId()) {
					
						if ($edit_line['is_deleted']=="1") {
							$l->delete();
						} else {
							$l->setIcon($edit_line['icon'])
							->setUrl($edit_line['url'])
							->setTitle($edit_line['title'])
							->save();
						}
					
					
					}
				}
			}
			$payload = [
				'success' => '1',
				'values' => $values,
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