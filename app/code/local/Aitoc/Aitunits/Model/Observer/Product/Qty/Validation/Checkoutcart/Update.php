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
class Aitoc_Aitunits_Model_Observer_Product_Qty_Validation_Checkoutcart_Update
    extends Aitoc_Aitunits_Model_Observer_Product_Qty_Validation_Abstract
{
    
    protected function _getAllowedRoutes()
    {
        return array(
            'checkout_cart_updatePost',
            'aiteditablecart_cart_updatePost'
        );
    }
    
    public function validate(Varien_Event_Observer $observer)
    {
        if(parent::validate($observer))
        {
            return true ;
        }
        
        $cart = $observer->getCart();
        $info = $observer->getInfo();
        foreach($info as $itemId=>$itemInfo)
        {
            if(!isset($itemInfo['qty']))
            {
               continue; 
            }
            $item = $cart->getQuote()->getItemById($itemId);
            $reqQtys = $this->_getReqQtys($item);
            $item = Mage::helper('aitunits')->getValidProduct($item);
            if($item->getProductType()=='simple'||$item->getProductType()=='virtual'||$item->getProductType()=='configurable')
            {
                $this->_validateProduct($item,$itemInfo['qty'],$reqQtys);
            }
        }
    }
    
    protected function _getReqQtys($item)
    {
        $summQty = $item->getQty();
        $reqQtys = Mage::helper('aitunits')->getAllowedQtys($item); 
        if(!in_array($summQty, $reqQtys))
        {
            $reqQtys[]=$summQty;
        }
        return $reqQtys;
    }
 
}