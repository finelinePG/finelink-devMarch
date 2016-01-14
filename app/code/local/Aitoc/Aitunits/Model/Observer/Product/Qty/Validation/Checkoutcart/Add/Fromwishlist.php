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
class Aitoc_Aitunits_Model_Observer_Product_Qty_Validation_Checkoutcart_Add_Fromwishlist
    extends Aitoc_Aitunits_Model_Observer_Product_Qty_Validation_Abstract
{
    
    protected function _getAllowedRoutes()
    {
        return array(
            'wishlist_index_cart',
        );
    }
    
    public function validate(Varien_Event_Observer $observer)
    {
        if(parent::validate($observer))
        {
            return true ;
        }
        
        $item = $observer->getProduct();
        $id = $item->getId();
        $item = Mage::helper('aitunits')->getValidProduct($item);
        $params = Mage::app()->getRequest()->getParams();
        $itemType = $item->getTypeId();
        if($itemType =='simple'||$itemType =='virtual'||$itemType =='configurable')
        {
            if(!isset($params['qty']))
            {
                return;
            }
            $itemQty = $params['qty'];
            $this->_validateProduct($item,$itemQty,$this->_getReqQtys($item));
            return;
        }

    }
    
    protected function _getReqQtys($item)
    {
        $reqQtys = Mage::helper('aitunits')->getAllowedQtys($item);
        $oldQty = $this->_getOldWishlistItem()->getQty();
        if(!in_array($oldQty, $reqQtys))
        {
            $reqQtys[]=$oldQty;
        }
        return $reqQtys;
    }
    
    protected function _getOldWishlistItem()
    {
        $itemId = (int) Mage::app()->getRequest()->getParam('item');
        /* @var $item Mage_Wishlist_Model_Item */
        $item = Mage::getModel('wishlist/item')->load($itemId);
        return $item;
    }
    
}