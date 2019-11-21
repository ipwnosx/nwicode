<?php

/**
 * Class WebView_Model_WebView
 *
 * @method string getBannerTextColor()
 * @method string getBannerBgColor()
 */
class WebView_Model_WebView extends Core_Model_Default
{
    /**
     * WebView_Model_WebView constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->_db_table = 'WebView_Model_Db_Table_WebView';
        return $this;
    }
}
