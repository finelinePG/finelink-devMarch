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
/* $meta=%default,Aitoc_Aitcg,Aitoc_Aitdownloadablefiles,Aitoc_Aitgroupedoptions,Aitoc_Aitmanufacturers% */
if(Mage::helper('core')->isModuleEnabled('Aitoc_Aitmanufacturers')){
    class Aitoc_Aitunits_Block_Rewrite_CatalogProductView_Aittmp extends Aitoc_Aitmanufacturers_Block_Rewrite_CatalogProductView {} 
 }elseif(Mage::helper('core')->isModuleEnabled('Aitoc_Aitgroupedoptions')){
    class Aitoc_Aitunits_Block_Rewrite_CatalogProductView_Aittmp extends Aitoc_Aitgroupedoptions_Block_Rewrite_FrontCatalogProductView {} 
 }elseif(Mage::helper('core')->isModuleEnabled('Aitoc_Aitdownloadablefiles')){
    class Aitoc_Aitunits_Block_Rewrite_CatalogProductView_Aittmp extends Aitoc_Aitdownloadablefiles_Block_Rewrite_FrontCatalogProductView {} 
 }elseif(Mage::helper('core')->isModuleEnabled('Aitoc_Aitcg')){
    class Aitoc_Aitunits_Block_Rewrite_CatalogProductView_Aittmp extends Aitoc_Aitcg_Block_Rewrite_Catalog_Product_View {} 
 }else{
    /* default extends start */
    class Aitoc_Aitunits_Block_Rewrite_CatalogProductView_Aittmp extends Mage_Catalog_Block_Product_View {}
    /* default extends end */
}

/* AITOC static rewrite inserts end */
class Aitoc_Aitunits_Block_Rewrite_CatalogProductView extends Aitoc_Aitunits_Block_Rewrite_CatalogProductView_Aittmp 
{
    
    public function getMinimalQty($product)
    {
        $oldQty = parent::getMinimalQty($product); 
        if(Mage::getVersion() > '1.4.2.0')
        {
            return $oldQty;
        }
        return $this->_aitunitsGetMinQty($product, $oldQty);
    }
    
    public function getProductDefaultQty($product = null)
    {
        if(Mage::getVersion() <= '1.4.2.0')
        {
            return;
        }
        $oldQty = parent::getProductDefaultQty($product);
        return $this->_aitunitsGetMinQty($product, $oldQty);
    }
    
    protected function _aitunitsGetMinQty($product, $oldQty)
    {
        if(!Mage::helper('aitunits')->getAvailableStores())
        {
            return $oldQty;
        }
        $helper = Mage::helper('aitunits');
        if(!isset($product))
        {
            $product = $this->getProduct();
        }
        $product = $helper->getValidProduct($product);
        if(!$helper->isUsableProduct($product))
        {
            return $oldQty;
        }
        $hasSelect = $helper->getProductAttributeValue($product, 'aitunits_select_form');  
        if(empty($hasSelect))
        {
            return $oldQty;
        }
        $minQty = Mage::helper('aitunits')->getMinAllowedQty($product);
        if($minQty >= $oldQty)
        {
            return $minQty; 
        }
        return $oldQty;
    }

}