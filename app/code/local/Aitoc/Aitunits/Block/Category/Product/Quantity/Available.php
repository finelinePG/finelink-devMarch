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
class Aitoc_Aitunits_Block_Category_Product_Quantity_Available extends Aitoc_Aitunits_Block_Category_Product_Decorator
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
        $isValidProduct = Mage::helper('aitunits')->isUsableProduct($this->getProduct());
        if(!$isValidProduct)
        {
            return false;
        }
        //-- Aitpreorder compatibilty
        $preorder = $this->getProduct()->getPreorder();
        if(isset($preorder)&& $preorder == true)
        {
            return false;
        }
        //--
        $value = $this->getProductAttributeValue('aitunits_instock_qty_show');
        if(!empty($value))
        {
            return true;
        }
        return false;
    }
    
    public function getQtyIndicator()
    {
        $iQty = (int)$this->getProductStockQty();
        $indicatorType = $this->getProductAttributeValue('aitunits_instock_qty_show');
        switch ($indicatorType)
        {
            case('number'):
                return $iQty;
            default:
                return $this->_getWordyIndicator();
        }
    }
    
    private function _getWordyIndicator()
    {
        $qty = $this->getProductStockQty();
        
        $highLimit = $this->getProductAttributeValue('aitunits_instock_qty_word_high');
        if($qty > $highLimit)
        {
            return Mage::helper('aitunits')->__('In Stock');
        }
        
        $mediumLimit = $this->getProductAttributeValue('aitunits_instock_qty_word_med');
        if($qty > $mediumLimit)
        {
            return Mage::helper('aitunits')->__('Sell out risk - Moderate');
        }
        
        $lowLimit = $this->getProductAttributeValue('aitunits_instock_qty_word_low');
        if($qty > $lowLimit)
        {
            return Mage::helper('aitunits')->__('Sell out risk - High');
        }
        if($qty <= $lowLimit && $qty > 0 )
        {
            return Mage::helper('aitunits')->__('Hurry Up! - Just few in stock');
        }
        return Mage::helper('aitunits')->__('Out of stock');
    }
    
    public function getJsParams()
    {
        $params =  array(
            'value'   => $this->getQtyIndicator(),
        );
        return Mage::helper('core')->jsonEncode($params);
    }
    
    public function getProductStockQty()
    {
        if(Mage::getVersion() <= '1.4.2.0')
        {
            return $this->getProduct()->getStockItem()->getQty();
        }
        return $this->getProduct()->getStockItem()->getStockQty();
    }        
}