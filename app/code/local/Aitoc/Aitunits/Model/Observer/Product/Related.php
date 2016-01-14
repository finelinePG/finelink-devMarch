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
class Aitoc_Aitunits_Model_Observer_Product_Related
    extends Aitoc_Aitunits_Model_Observer_Abstract
{
    protected function _getAllowedRoutes()
    {
        return array(
            'checkout_cart_add',
            'aiteditablecart_cart_updatePost'
        );
    }
    
    public function replaceQty(Varien_Event_Observer $observer)
    {
        if( !in_array( $this->_getRoute() , $this->_getAllowedRoutes()) )
        {
            return ;
        }
        
        $items = $observer->getItems();
        $relatedProductIds = explode(',', Mage::app()->getRequest()->getParam('related_product'));
        if(!isset($items)||!isset($relatedProductIds))
        {
            return;
        }
        foreach($items as $item)
        {
            $product = Mage::helper('aitunits')->getValidProduct($item);
            $isReqProduct = in_array($product->getId(), $relatedProductIds);
            if(!$isReqProduct)
            {
                continue;
            }
            $producType = $product->getTypeId(); 
            if($producType!=='simple'&& $producType!=='virtual'&& $producType!=='configurable')
            {
                continue;
            }
            $isSelectView = Mage::helper('aitunits')->getProductAttributeValue($product, 'aitunits_select_form');
            $hasLimitedQty = Mage::helper('aitunits')->getProductAttributeValue($product,'aitunits_allowed_qty_input');
            if(!isset($isSelectView)||!isset($hasLimitedQty))
            {
                continue;
            }
            $qtys = Mage::helper('aitunits')->getAllowedQtys($product);
            $item->setQty(min($qtys));
        }
    }
}