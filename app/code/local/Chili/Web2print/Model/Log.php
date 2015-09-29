<?php
class Chili_Web2print_Model_Log extends Mage_Core_Model_Abstract{
    public function _construct()
    {
        parent::_construct();
        $this->_init('web2print/log');
    }
}