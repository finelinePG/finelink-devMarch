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
/* $meta=%default,Aitoc_Aitbestsellers% */
if(Mage::helper('core')->isModuleEnabled('Aitoc_Aitbestsellers')){
    class Aitoc_Aitunits_Block_Rewrite_CatalogProductList_Aittmp extends Aitoc_Aitbestsellers_Block_Rewrite_CatalogProductList {} 
 }else{
    /* default extends start */
    class Aitoc_Aitunits_Block_Rewrite_CatalogProductList_Aittmp extends Mage_Catalog_Block_Product_List {}
    /* default extends end */
}

/* AITOC static rewrite inserts end */
class Aitoc_Aitunits_Block_Rewrite_CatalogProductList extends Aitoc_Aitunits_Block_Rewrite_CatalogProductList_Aittmp
{
    
    protected function _toHtml()
    {
        $this->setType('catalog/product_list');
        $html = parent::_toHtml();
        $html = Mage::helper('aitunits/event')->addAfterToHtml($html,$this);
        return $html;
    }
}