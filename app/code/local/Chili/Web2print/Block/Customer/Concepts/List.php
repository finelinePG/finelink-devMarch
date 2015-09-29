<?php
class Chili_Web2print_Block_Customer_Concepts_List extends Mage_Core_Block_Template
{

    /**
     * Constructor, gets and sets the featured product collection
     */
    public function __construct()
    {
        parent::__construct();

        $session = Mage::getSingleton('customer/session');
        $customerId= $session->getId();

        $collection = Mage::getModel('web2print/concept')->getCollection()->addFieldToFilter('customer_id', $customerId)->load();
        $this->_conceptCollection = $collection;
    }

    /**
     *
     * @return type concept collection for customer
     *      */
    public function getConceptCollection(){
        return $this->_conceptCollection;
    }

    public function getImage($documentId){
         if ($documentId) {
            $cache = $this->getCacheImage($documentId);
            if ($cache) {
                return $cache;
            }
            return $this->_getChiliImageUrl($documentId);
        }
    }
    
    private function getCacheImage($documentId){
         $imgProfile = Mage::helper('catalog/image')->getImageConversionProfile('category');        
       $cacheName = 'image_'.$imgProfile.'_'.$documentId;
       $cacheModel  = Mage::getSingleton('core/cache');
       $cacheImgUrl = $cacheModel->load($cacheName);

       if($cacheImgUrl){           
           return $cacheImgUrl;
       }else{
           return false;
       }      
    }
     protected function _getChiliImageUrl($documentId)
    {
        $controller = 'concept';
        $ajaxUrl = Mage::getUrl('web2print/ajax/image/');
        $url = Mage::helper('core/url')->addRequestParam($ajaxUrl,
            array(
                 'controller' => $controller,
                 'documentId' => $documentId,
                 'redirect' => true,
            )
        );
        return $url;
    }
    /**
     *
     * @return type products in concept collection

    public function getProducts(){
        $products=array();
        foreach ($this->_conceptCollection as $conceptItem)
        {
            $product = Mage::getModel('catalog/product')->load($conceptItem->getProductId());
            array_push($products, $product);
        }

        return $products;
    }*/

}
