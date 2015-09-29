<?php

class Chili_Web2print_Block_Product_List extends Mage_Catalog_Block_Product_List
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