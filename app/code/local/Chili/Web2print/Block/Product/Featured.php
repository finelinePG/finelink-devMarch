<?php

class Chili_Web2print_Block_Product_Featured extends Chili_Web2print_Block_Product_List
{
   /**
    * Constructor, gets and sets the featured product collection
    */
    public function __construct()
    {
       parent::__construct();
       $this->setTemplate('web2print/product/featured.phtml');      
       
        $collection = Mage::getResourceModel('catalog/product_collection');

//        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
//        $attributes[] = 'web2print_document_id';
        $attributes[] = 'featured';
        
        $collection->addAttributeToSelect($attributes)
        ->addAttributeToFilter('featured', 1);

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        $this->_productCollection = $collection;       
    }
    
    /**
     *
     * @return type featured products collection
     *      */
    public function getProductCollection(){
        return $this->_productCollection;
    }   
    
    /**
     *
     * @return type int
     * returns number of columns the grid should have
     */
    public function getColumnCount(){
        return $this->getColumns();
    }
	
}