<?php
/**
 * Product Units and Quantities
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitunits
 * @version      1.0.11
 * @license:     0JdTQfDMswel7fbpH42tkXIHe3fixI4GH3daX0aDVf
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2012 AITOC, Inc. 
 */
class Aitoc_Aitunits_Model_Observer_Cart 
{
    
    private $_summQty;
    
    public function addedItemQtyValidate(Varien_Event_Observer $observer)
    {
        if(!Mage::helper('aitunits')->getAvailableStores())
        {
            return false;
        }

        /* {#AITOC_COMMENT_END#}
        $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('Aitoc_Aitunits')->getLicense()->getPerformer();
        $ruler = $performer->getRuler();
        $groupId = Mage::app()->getGroup()->getId();
        if(!in_array($groupId, $ruler->getAvailableStores()))
        {
            return false;
        }
        {#AITOC_COMMENT_START#} */
        $item = $observer->getProduct();
        $item = Mage::helper('aitunits')->getValidProduct($item);
        $params = Mage::app()->getRequest()->getParams();
        $itemType = $item->getTypeId();
        switch ($itemType)
        {
            case('grouped'):
                $products = $item->getTypeInstance(true)->getAssociatedProducts($item);
                $aProductQtys = $params['super_group'];
                foreach($products as $product)
                {
                    $productQty = $aProductQtys[$product->getId()]; 
                    $this->_validateProduct($product,$productQty);
                }
                return;
            case('simple'||'virtual'||'configurable'):
                $itemQty = $params['qty'];
                $this->_validateProduct($item,$itemQty);
                return;
        }
    }
    
    public function updatedItemsQtyValidate(Varien_Event_Observer $observer)
    {
        if(!Mage::helper('aitunits')->getAvailableStores())
        {
            return false;
        }

        /* {#AITOC_COMMENT_END#}
        $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('Aitoc_Aitunits')->getLicense()->getPerformer();
        $ruler = $performer->getRuler();
        $groupId = Mage::app()->getGroup()->getId();
        if(!in_array($groupId, $ruler->getAvailableStores()))
        {
            return false;
        }
        {#AITOC_COMMENT_START#} */
        $cart = $observer->getCart();
        $info = $observer->getInfo();
        foreach($info as $itemId=>$itemInfo)
        {
            if(!isset($itemInfo['qty']))
            {
               continue; 
            }
            $item = $cart->getQuote()->getItemById($itemId);
            $this->_summQty = $item->getQty();
            $item = Mage::helper('aitunits')->getValidProduct($item);
            if($item->getProductType() == 'simple'||'virtual'||'configurable')
            {
                $this->_validateProduct($item,$itemInfo['qty']);
            }
        }
    }
    
    protected function _validateProduct($product,$qty)
    {
        $isAllowedInputOnly = Mage::helper('aitunits')->getProductAttributeValue($product,'aitunits_allowed_qty_input') ;
        if(!empty($isAllowedInputOnly))
        {
            $availableQty = $qty;
            $requiredQtys = Mage::helper('aitunits')->getAllowedQtys($product);
            if(!empty($this->_summQty))
            {
                if(!in_array($this->_summQty, $requiredQtys))
                {
                    $requiredQtys[]=$this->_summQty;
                }
            }
            $isValidQty = false;
            foreach($requiredQtys as $requiredQty)
            {
                if($requiredQty ==$availableQty)
                {
                    $isValidQty = true;
                }
            }
            if(!$isValidQty)
            {
                $message = 'A Qty of '.$product->getName().' can not be added.Please set another Qty.';
                Mage::throwException(Mage::helper('aitunits')->__($message));
            }
        }
    }
    
}