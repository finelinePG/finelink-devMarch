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
class Aitoc_Aitunits_Block_Rewrite_CatalogProductPrice extends Mage_Catalog_Block_Product_Price
{
    
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        $html = Mage::helper('aitunits/event')->addAfterToHtml($html,$this);
        return $html;
    }
}