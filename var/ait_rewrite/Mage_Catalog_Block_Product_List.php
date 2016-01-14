<?php
/* DO NOT MODIFY THIS FILE! THIS IS TEMPORARY FILE AND WILL BE RE-GENERATED AS SOON AS CACHE CLEARED. */


class Aitoc_Aitunits_Block_Rewrite_CatalogProductList extends Mage_Catalog_Block_Product_List
{
    public function getAddToCartUrl($product, $additional = array()) {
        if($product->getWeb2printDocumentId() == ''){
            return parent::getAddToCartUrl($product, $additional);
        }else{
            switch($product->getTypeId()){
                case 'configurable':
                case 'bundle':
                    return $product->getProductUrl();
                    break;
                default:
                    return $this->helper('web2print')->getAddToCartUrl($product, $this);
                    break;
            }
        }
    }
	
}


class Chili_Web2print_Block_Product_List extends Aitoc_Aitunits_Block_Rewrite_CatalogProductList
{
    
    protected function _toHtml()
    {
        $this->setType('catalog/product_list');
        $html = parent::_toHtml();
        $html = Mage::helper('aitunits/event')->addAfterToHtml($html,$this);
        return $html;
    }
}

