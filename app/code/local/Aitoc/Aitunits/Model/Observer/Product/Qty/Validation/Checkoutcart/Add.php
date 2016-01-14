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
class Aitoc_Aitunits_Model_Observer_Product_Qty_Validation_Checkoutcart_Add
    extends Aitoc_Aitunits_Model_Observer_Product_Qty_Validation_Abstract
{
    
    protected function _getAllowedRoutes()
    {
        return array(
            'checkout_cart_add',
        );
    }
    
    public function validate(Varien_Event_Observer $observer)
    {
        if(parent::validate($observer))
        {
            return true ;
        }
        
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
                    $this->_validateProduct($product,$productQty,$this->_getReqQtys($product));
                }
                return;
            case('simple'||'virtual'||'configurable'):
                $itemQty = $params['qty'];
                $this->_validateProduct($item,$itemQty,$this->_getReqQtys($item));
                return;
        }
        
    }
    
}