<?php
class Chili_Web2print_Model_Mysql4_Log extends Mage_Core_Model_Mysql4_Abstract {
    public function _construct() {
        $this->_init('web2print/log', 'log_id');                      
    }

}