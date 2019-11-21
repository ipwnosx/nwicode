<?php
class Followus_Model_Followus_Line extends Core_Model_Default {
    public function __construct($params = array()) {
        parent::__construct($params);
        $this->_db_table = 'Followus_Model_Db_Table_Followus_Line';
        return $this;
    }
}
