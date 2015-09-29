<?php

class Chili_Web2print_Block_Adminhtml_Catalog_Product_Edit_Tab_Web2print extends Mage_Core_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface {
	
	public function __construct()
	{
		parent::__construct();
        $this->setTemplate('web2print/product/edit/web2print.phtml');
	}
	
	public function getTabLabel(){
		return Mage::helper('core')->__('CHILI Web2Print');
	}
	
	public function getTabTitle(){
		return Mage::helper('core')->__('CHILI Web2Print');
	}
	
	public function canShowTab(){
        $product = Mage::registry('product');
        return $product->getId();
	}
	
	public function isHidden(){
		return false;
	}

    public function getAfter()
    {
        return 'group_8';
    }
}
