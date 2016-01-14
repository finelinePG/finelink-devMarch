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
abstract class Aitoc_Aitunits_Model_Observer_Product_Qty_Validation_Abstract
    extends Aitoc_Aitunits_Model_Observer_Abstract  
{
    
    protected function _init()
    {
        parent::_init();
    }
    
    public function validate(Varien_Event_Observer $observer)
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
            return true;
        }
        {#AITOC_COMMENT_START#} */
        if( !in_array( $this->_getRoute() , $this->_getAllowedRoutes()) )
        {
            return true ;
        }
        return false;
    }
    
    protected function _validateProduct($product,$qty,$aReqQtys = array())
    {
        $isSelect = Mage::helper('aitunits')->getProductAttributeValue($product,'aitunits_select_form');
        if(empty($isSelect))
        {
            return;
        }
        $isAllowedInputOnly = Mage::helper('aitunits')->getProductAttributeValue($product,'aitunits_allowed_qty_input') ;
        if(!empty($isAllowedInputOnly))
        {
            $availableQty = $qty;
            if(empty($aReqQtys))
            {
                $aReqQtys = Mage::helper('aitunits')->getAllowedQtys($product);
            }
            $isValidQty = false;
            foreach($aReqQtys as $requiredQty)
            {
                if($requiredQty ==$availableQty)
                {
                    $isValidQty = true;
                }
            }
            if(!$isValidQty)
            {
                $message = 'A Qty of '.$product->getName().' can not be added. Please set another Qty.';
                Mage::throwException(Mage::helper('aitunits')->__($message));
            }
        }
    }
    
    protected function _getReqQtys($item)
    {
        $reqQtys = Mage::helper('aitunits')->getAllowedQtys($item); 
        return $reqQtys;
    }
}