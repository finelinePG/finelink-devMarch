<?php
class Chili_Web2print_Block_Product_Widget_New extends Mage_Catalog_Block_Product_Widget_New{
    public function getAddToCartUrl($product, $additional = array()) {
        if($product->getWeb2printDocumentId() == ''){
            return parent::getAddToCartUrl($product, $additional);
        }else{
            return $this->helper('web2print')->getEditorUrl($product->getId());
        }
    }
}