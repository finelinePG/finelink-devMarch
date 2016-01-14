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
class Aitoc_Aitunits_Block_Category_Product_Unit extends Aitoc_Aitunits_Block_Category_Product_Decorator
{
    
    public function isRenderBlock()
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
        $value = $this->getProductAttributeValue('aitunits_unit_enable');
        if(!empty($value))
        {
            return true;
        }
        return false;
    }
    
    public function getIndicator()
    {
        $unit = $this->getProductAttributeValue('aitunits_unit_value');
        $unit = Mage::helper('aitunits')->__($unit);
        $divider = Mage::helper('aitunits')->__($this->_getDivider());
        $indicator = ' '.$divider.' '.$unit;
        return $indicator;
    }
    
    public function getJsParams()
    {
        $params =  array(
            'value'   => $this->getIndicator(),
            'itemId'  => $this->getItemId(),
        );
        return Mage::helper('core')->jsonEncode($params);
    }
    
    private function _getDivider()
    {
        $dividerType = $this->getProductAttributeValue('aitunits_unit_divider');
        switch ($dividerType)
        {
            case('slash'):
                return '/';
            default:
                return $dividerType;
        }
    }
    
}