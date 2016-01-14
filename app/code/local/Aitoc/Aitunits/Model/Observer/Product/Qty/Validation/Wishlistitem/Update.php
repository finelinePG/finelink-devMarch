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
class Aitoc_Aitunits_Model_Observer_Product_Qty_Validation_Wishlistitem_Update 
    extends Aitoc_Aitunits_Model_Observer_Product_Qty_Validation_Abstract
{
    public function validate(Varien_Event_Observer $observer)
    {
        if(parent::validate($observer))
        {
            return true ;
        }
        $item = $observer->getItem();
        $item = Mage::helper('aitunits')->getValidProduct($item);
        
        if($item->getProductType()=='simple'||$item->getProductType()=='virtual'||$item->getProductType()=='configurable')
        {
            try 
            {
                $this->_validateProduct($item,$item->getQty(),$this->_getReqQtys($observer->getItem()));
            } 
            catch (Exception $e) 
            {
                Mage::getSingleton('customer/session')->addError(
                    Mage::helper('aitunits')->__($e->getMessage())
                );
            }
        }
    }
    
    protected function _getAllowedRoutes()
    {
        return array(
            'wishlist_index_update',
        );
    }
    
}