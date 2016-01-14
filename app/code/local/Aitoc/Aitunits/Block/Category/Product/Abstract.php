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
abstract class Aitoc_Aitunits_Block_Category_Product_Abstract 
    extends Mage_Core_Block_Template
{
    
    protected $_item;
    protected $_product;
    protected $_hasContainer = false;
    
    public function setItem($item)
    {
        $this->_item = $item;
        return $this;
    }
    
    public function getItem()
    {
        return $this->_item;
    }
    
    public function getProduct()
    {
        $product = $this->_product;
        if(empty($product))
        {
            $product = $this->getItem()->getProduct();
        }
        return $product;
    }
    
    public function setProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_product = $product;
        return $this;
    }
    
    public function getProductId()
    {
        return $this->getProduct()->getId();
    }
    
    public function getProductAttributeValue($attributeCode)
    {
        return Mage::helper('aitunits')->getProductAttributeValue($this->getProduct(),$attributeCode);
    }
    
    public function getId()
    {
        return $this->getSuffixExpr().$this->getItemId();
    }
    
    public function getItemId()
    {
        $item = $this->getItem();
        if(isset($item))
        {
            return $item->getId();
        }
        else
        {
            return $this->getProduct()->getId();
        }
    }
    
    public function addContainer()
    {
        $this->_hasContainer = true;
        return $this;
    }
    
    public function hasContainer()
    {
        return $this->_hasContainer; 
    }
    
    public function getSuffixExpr()
    {
        $suffix = $this->getSuffix();
        if(isset($suffix)&& !empty($suffix))
        {
            return $suffix.'_';
        }
    }
 
}