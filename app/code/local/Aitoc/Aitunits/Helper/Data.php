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
class Aitoc_Aitunits_Helper_Data extends Mage_Core_Helper_Abstract
{
     /**
     *  Allowed Aitunits section in configuration
     *
     */
    const XPATH_CONFIG_AITUNITS_SELECT_FORM              = 'cataloginventory/aitunits/select_form';
    //const XPATH_CONFIG_AITUNITS_ALLOWED_QTY_INPUT        = 'cataloginventory/aitunits/allowed_qty_input';
    const XPATH_CONFIG_AITUNITS_ALLOWED_QTY_VALUES       = 'cataloginventory/aitunits/allowed_qty_values';
    //const XPATH_CONFIG_AITUNITS_ALLOWED_QTY_BEYOND       = 'cataloginventory/aitunits/allowed_qty_beyond';
    const XPATH_CONFIG_AITUNITS_UNIT_ENABLE              = 'cataloginventory/aitunits/unit_enable';
    const XPATH_CONFIG_AITUNITS_UNIT_VALUE               = 'cataloginventory/aitunits/unit_value';
    const XPATH_CONFIG_AITUNITS_UNIT_DIVIDER             = 'cataloginventory/aitunits/unit_divider';
    const XPATH_CONFIG_AITUNITS_INSTOCK_QTY_SHOW         = 'cataloginventory/aitunits/instock_qty_show';
    //const XPATH_CONFIG_AITUNITS_INSTOCK_QTY_WORD_FULL    = 'cataloginventory/aitunits/instock_qty_word_full';
    const XPATH_CONFIG_AITUNITS_INSTOCK_QTY_WORD_HIGH    = 'cataloginventory/aitunits/instock_qty_word_high';
    const XPATH_CONFIG_AITUNITS_INSTOCK_QTY_WORD_MED     = 'cataloginventory/aitunits/instock_qty_word_med';
    const XPATH_CONFIG_AITUNITS_INSTOCK_QTY_WORD_LOW     = 'cataloginventory/aitunits/instock_qty_word_low';
    
    public function getProductAttributeValue($product, $attributeCode)
    {
        $product = $this->getValidProduct($product);
        if(!$this->isValidAttribute($product,$attributeCode))
        {
            return false;
        }
        
        $value = $product->getData($attributeCode);
        if(!isset($value)|| $value=='')
        {
            $constName = 'XPATH_CONFIG_'.strtoupper($attributeCode);
            $constValue = constant('Aitoc_Aitunits_Helper_Data::'.$constName); 
            $value = Mage::app()->getStore()->getConfig($constValue);
            if(!isset($value))
            {
                Mage::throwException('Invalid product attribute code');
                return false;
            }
        }
        return $value;
    }
    
    public function isValidAttribute($product,$attributeCode)
    {
        $product = $this->getValidProduct($product);
        $attributes = $product->getAttributes();
        foreach($attributes as $code => $attributeEntity )
        {
            if($code == $attributeCode)
            {
                return true;
            }
        }
        return false;
    }
    
    protected function _getAllowedQtys($product)
    {
        $product = $this->getValidProduct($product);
        $sValues = $this->getProductAttributeValue($product, 'aitunits_allowed_qty_values');
        $aValues = explode(',', $sValues);
        foreach($aValues as $key => $value)
        {
            $aValues[$key] = intval($value);
        }
        return $aValues;
    }
    
    public function getAllowedQtys($product)
    {
        $product = $this->getValidProduct($product);
        $minQty = $this->getMinAllowedQty($product);
        $qtys = $this->_getAllowedQtys($product);
        $filtredQtys = array();
        foreach($qtys as $qty)
        {
            if($qty >= $minQty)
            {
                $filtredQtys[] = $qty;
            }
        }
        return $filtredQtys; 
    }
    
    // get valid product from object
    public function getValidProduct($obj)
    {
        switch(true)
        {
            case ($obj instanceof Mage_Catalog_Model_Product):
                $product = $obj;
                break;
            case ($obj instanceof Mage_Sales_Model_Quote_Item):
                $product = $obj->getProduct();
                break;
            case ($obj instanceof Mage_Wishlist_Model_Item):
                $product = $obj->getProduct();
                break;
            default:
                Mage::throwException(Mage::helper('aitunits')->__('Aitunits: Invalid product object'));
        }
        return $product;
    }
    
    // usable product in Aitunits module context
    public function isUsableProduct(Mage_Catalog_Model_Product $product)
    {
        $stockItem = $product->getStockItem();
        if(!isset($stockItem))
        {
            return false;
        }
        if($this->isValidProductType($product) && $stockItem->getIsInStock())
        {
            return true;
        }
        
    }
    
    public function isValidProductType(Mage_Catalog_Model_Product $product)
    {
        $type = $product->getTypeId();
        if($type=='simple'||$type=='virtual'||$type=='configurable')
        {
            return true;
        }
        return false;
    }
    
    public function getMinAllowedQty($product)
    {
        $product = $this->getValidProduct($product);
        if(!$this->isUsableProduct($product))
        {
            return null;
        }
        $aQtys = $this->_getAllowedQtys($product);
        sort($aQtys,SORT_NUMERIC);
        $minQty = $product->getStockItem()->getMinSaleQty();
        $minQty = !isset($minQty)?0:$minQty;
        if($minQty < 0)
        {
            return null;
        }
        
        foreach($aQtys as $qty )
        {
            if($qty == $minQty)
            {
                return $qty;
            }
            if($qty < $minQty)
            {
                continue;
            }
            return $qty;
        }
    }
    
    public function getFormRequestParams($formName)
    {
        $requestParams = Mage::app()->getRequest()->getParams();
        if(!isset($requestParams[$formName]))
        {
            return false;
        }
        return $requestParams[$formName]; 
    }
    
    public function getAttributeGroupName()
    {
        return 'Product Units and Quantities Options';
    }
    
    public function getAvailableStores()
    {
        $stores = Mage::getStoreConfig("aitsys/modules/Aitoc_Aitunits",0);
        if (!$stores)
        {
            return true;
        }
        $stores = explode(",", $stores);
            
        $store = Mage::app()->getStore()->getGroupId();
        
        if(in_array($store, $stores))
        {
            return true;
        }
        
        return false;
    }
}