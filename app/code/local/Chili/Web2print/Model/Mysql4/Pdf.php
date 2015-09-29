<?php

class Chili_Web2print_Model_Mysql4_Pdf extends Mage_Core_Model_Mysql4_Abstract {
    public function _construct() {
        $this->_init('web2print/pdf', 'pdf_id');                      
    }
}