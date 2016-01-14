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
abstract class Aitoc_Aitunits_Model_Observer_Product_Qty_Changer_Abstract
    extends Aitoc_Aitunits_Model_Observer_Abstract
{
    
    public function change(Varien_Event_Observer $observer)
    {
        if( !in_array( $this->_getRoute() , $this->_getAllowedRoutes()) )
        {
            return ;
        }
        $this->_initEvent($observer);
        
        $product = $this->_getProduct();
        if(!(isset($product)&&!empty($product)))
        {
            return;
        }
        
        if(!$this->_isRequiredProduct($product))
        {
            return;
        }
        $this->_change($product);
    }
    
    protected function _isRequiredProduct($product)
    {
        $helper = Mage::helper('aitunits');
        if(!$helper->isUsableProduct($product))
        {
            return false;
        }
        $hasSelect = $helper->getProductAttributeValue($product, 'aitunits_select_form');  
        if(empty($hasSelect))
        {
            return false;
        }
        return true;
    }
    
    protected function _getProduct()
    {
        $product = $this->_getEvent()->getProduct();
        return Mage::helper('aitunits')->getValidProduct($product);
    }
    
    protected function _getOfFormQty()
    {
        $requestParams = Mage::app()->getRequest()->getParams();
        $currentQty =  $requestParams['qty'];
        if(!isset($currentQty))
        {
            return 0;
        }
        return $currentQty; 
    }
    
    protected function _change($product)
    {
        $currentQty = $this->_getOfFormQty();
        $helper = Mage::helper('aitunits');
        if ($currentQty == 0) 
        {
            $quote = $this->_getEvent()->getQuoteItem();
            $qtyToAdd = $quote->getQtyToAdd();
            $quote->addQty($helper->getMinAllowedQty($product)- $qtyToAdd);
        }
    }
    
}