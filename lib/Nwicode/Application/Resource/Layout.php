<?php

/**
 * Class Nwicode_Application_Resource_Layout
 */
class Nwicode_Application_Resource_Layout
    extends Zend_Application_Resource_Layout
{
    /**
     * @return \Nwicode\Layout|Zend_Layout
     * @throws Zend_Layout_Exception
     */
    public function getLayout()
    {
        if (null === $this->_layout) {
            $this->_layout = Nwicode\Layout::startMvc($this->getOptions());
        }
        return $this->_layout;
    }
}
