<?php
class Followus_Model_Followus extends Core_Model_Default {
    
	protected $_lines;
	public function __construct($params = array()) {
        parent::__construct($params);
        $this->_db_table = 'Followus_Model_Db_Table_Followus';
        return $this;
    }
	
	
	public function getLines() {
		if (!$this->_lines) {
			$line_model = new Followus_Model_Followus_Line();
			$lines = $line_model->findAll(['followus_id' => $this->getId()]);
			foreach ($lines as $line) {

				$l = new Followus_Model_Followus_Line();
				$l->find($line->getFollowusLineId());
				if ($l->getId()) $this->_lines[]=$l;
			}
		}
		return $this->_lines;
	}
}
