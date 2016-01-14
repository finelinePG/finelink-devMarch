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
class Aitoc_Aitunits_Model_Observer_Product_Qty_Changer_Addtoregistry
    extends Aitoc_Aitunits_Model_Observer_Product_Qty_Changer_Abstract
{
    
    protected function _getAllowedRoutes()
    {
        return array(
            'adjgiftreg_event_addItem',
        );
    }

    protected function _getProduct()
    {
        $obj = $this->_getEvent()->getObject();
        if(!($obj instanceof AdjustWare_Giftreg_Model_Item ))
        {
            return false;
        }
        $productId = $obj->getProductId(); 
        $product = Mage::getModel('catalog/product')->load($productId);
        return Mage::helper('aitunits')->getValidProduct($product);
    }
    
    protected function _getOfFormQty()
    {
        $requestParams = Mage::app()->getRequest()->getParams();
        $formName = Mage::getBlockSingleton('aitunits/category_product_form')->getId();
        $formData = Mage::registry($formName);
        if(!isset($formData['item']))
        {
            return 0;
        }
        $items = $formData['item'];
        $productId = $requestParams['product'];
        foreach($items as $key=>$item)
        {
            if($key == $productId)
            {
                return intval($item['qty']);
            }
        }
        return 0 ;
    }
    // add qty
    protected function _change($product)
    {
        $quote = $this->_getEvent()->getObject();
        if(!$this->_hasMark($quote))
        {
            return;
        }
        if(!$quote->hasData('item_id'))
        {
            return;
        }
        $currentQty = $this->_getOfFormQty();
        $helper = Mage::helper('aitunits');
        if ($currentQty == 0) 
        {
            $currentQty = $helper->getMinAllowedQty($product); 
        }

        $oldQty = $quote->getNumWants();
        $quote->setNumWants($oldQty + $currentQty - 1);
        $quote->getAitunitsMark()->removeHandler(get_class($this));
    }
    
}