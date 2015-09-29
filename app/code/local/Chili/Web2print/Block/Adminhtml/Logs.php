<?php
    class Chili_Web2print_Block_Adminhtml_Logs extends Mage_Adminhtml_Block_Widget_Grid_Container{
        
        public function __construct()
        {
            $this->_controller = 'adminhtml_logs';
            $this->_blockGroup = 'web2print';
            $this->_headerText = Mage::helper('web2print')->__('Logs overview');
            parent::__construct();       
        }
    }
?>
