<?php

class Followus_Mobile_ViewController extends Application_Controller_Mobile_Default {

    public function findAction() {

		$data['dd']="1";
        if($value_id = $this->getRequest()->getParam('value_id')) {

            try {
			
				$option = $this->getCurrentOptionValue();
				$followus = (new Followus_Model_Followus())->find(["value_id" => $value_id]);


				$lines = array();
				foreach($followus->getLines() as $line) $lines[]=$line->getData();
		
                $data = array(
                    "followus" => $followus->getData(),
					"lines" => $lines
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