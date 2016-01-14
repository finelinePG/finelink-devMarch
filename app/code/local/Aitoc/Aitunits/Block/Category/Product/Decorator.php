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
class Aitoc_Aitunits_Block_Category_Product_Decorator 
    extends Aitoc_Aitunits_Block_Category_Product_Abstract
{
    protected $_helperBlock;
    
    public function initBlock($context)
    {
        switch($context)
        {
            case('catalog_product_view'):
                $this->setProduct(Mage::helper('catalog')->getProduct());
                $this->addContainer();
            default:
                return;
        }
    }
    
    public function getHelperBlock()
    {
        return $this->_helperBlock;
    }
    
    public function setHelperBlock(Aitoc_Aitunits_Block_Category_Product_Abstract $blockObj)
    {
        $this->_helperBlock = $blockObj;
        return $this;
    }
    
}