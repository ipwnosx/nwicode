<?php

class Backoffice_Model_Moduleuninstall extends Core_Model_Default {

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->_db_table = 'Backoffice_Model_Db_Table_Moduleuninstall';
        return $this;
    }
 
    public function getModuleCode($name) {
        return $this->getTable()->getModuleCode($name);
    }
    public function clearScrap($module_option_id,$module_name) {
        return $this->getTable()->clearScrap($module_option_id,$module_name);
    }
    public function tablesDeletion($tables) {
        
        return $this->getTable()->tablesDeletion($tables);
    }
}
