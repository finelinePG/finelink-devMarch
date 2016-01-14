<?php
/**
 * All-In-One Checkout v1.0.15 : All-In-One Checkout v1.0.15 (CFM Unit)
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcheckout / Aitoc_Aitcheckoutfields
 * @version      1.0.15 - 2.9.15
 * @license:     jC7sr77MwqoHj2SDR8w4YXR3o3w7irXLNFUdRYpgyc
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitcheckoutfields_Block_Orderedit_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_order_id = null;
    
    public function __construct()
    {
        $oFront = Mage::app()->getFrontController();
        
        $iOrderId = $oFront->getRequest()->getParam('order_id');
             
        $this->_order_id = $iOrderId;   

        parent::__construct();
    }
    
    public function getSaveUrl()
    {
        return $this->getUrl('*/index/ordersave', array('order_id' => $this->_order_id));
    }
    
    public function getBackUrl()
    {
        return $this->getUrl('adminhtml/sales_order/view', array('order_id'=>$this->_order_id));
    }
    
    public function getHeaderText()
    {
        return Mage::helper('aitcheckoutfields')->__('Edit Order Custom Data');
    }
    
	protected function _prepareLayout()
    {
        $this->setChild('form', $this->getLayout()->createBlock('aitcheckoutfields/orderedit_edit_form'));
        return Mage_Adminhtml_Block_Widget_Container::_prepareLayout();
    }
    
    
}