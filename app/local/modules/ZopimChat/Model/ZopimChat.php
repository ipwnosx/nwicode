<?php
class ZopimChat_Model_ZopimChat extends Core_Model_Default {

    public function __construct($params = array()) {
        parent::__construct($params);
        $this->_db_table = 'ZopimChat_Model_Db_Table_ZopimChat';
        return $this;
    }

}
