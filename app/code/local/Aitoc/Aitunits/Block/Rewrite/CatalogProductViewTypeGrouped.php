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
/* AITOC static rewrite inserts start */
/* $meta=%default,Aitoc_Aitdownloadablefiles% */
if(Mage::helper('core')->isModuleEnabled('Aitoc_Aitdownloadablefiles')){
    class Aitoc_Aitunits_Block_Rewrite_CatalogProductViewTypeGrouped_Aittmp extends Aitoc_Aitdownloadablefiles_Block_Rewrite_FrontCatalogProductViewTypeGrouped {} 
 }else{
    /* default extends start */
    class Aitoc_Aitunits_Block_Rewrite_CatalogProductViewTypeGrouped_Aittmp extends Mage_Catalog_Block_Product_View_Type_Grouped {}
    /* default extends end */
}

/* AITOC static rewrite inserts end */
class Aitoc_Aitunits_Block_Rewrite_CatalogProductViewTypeGrouped extends Aitoc_Aitunits_Block_Rewrite_CatalogProductViewTypeGrouped_Aittmp
{
    
    protected function _toHtml()
    {
        $this->setType('catalog/product_view_type_grouped');
        $html = parent::_toHtml();
        $html = Mage::helper('aitunits/event')->addAfterToHtml($html,$this);
        return $html;
    }
}