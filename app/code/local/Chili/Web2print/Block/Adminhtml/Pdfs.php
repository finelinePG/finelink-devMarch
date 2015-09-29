<?php
    class Chili_Web2print_Block_Adminhtml_Pdfs extends Mage_Adminhtml_Block_Widget_Grid_Container{
        
        public function __construct()
        {
            $this->_controller = 'adminhtml_pdfs';
            $this->_blockGroup = 'web2print';
            $this->_headerText = Mage::helper('web2print')->__('Pdf overview');
            parent::__construct();       
            $this->_removeButton('add');
        }
    }
?>
