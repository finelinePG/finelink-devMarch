<?php
class Chili_Web2Print_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection{
    /**
     * Add web2print_document_id to collection when exists 
     */
    public function _construct(){
        parent::_construct();
        $this->addAttributeToSelect('web2print_document_id');
    }
}