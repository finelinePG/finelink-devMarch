<?php

class Chili_Web2print_Helper_Image extends Mage_Catalog_Helper_Image
{
   private $_productOption = null;
    
   /**
    *
    * @param Mage_Catalog_Model_Product $product
    * @param type $attributeName
    * @param type $imageFile
    * @return Chili_Web2print_Helper_Image 
    */
    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile=null)
    {
        $this->_reset();
        $this->_setModel(Mage::getModel('catalog/product_image'));
        $this->_getModel()->setDestinationSubdir($attributeName);
        $this->setProduct($product);

        $this->setWatermark(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_image"));
        $this->setWatermarkImageOpacity(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_imageOpacity"));
        $this->setWatermarkPosition(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_position"));
        $this->setWatermarkSize(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_size"));

        if ($imageFile) {
            $this->setImageFile($imageFile);
        } else {
            // add for work original size
            $this->_getModel()->setBaseFile( $this->getProduct()->getData($this->_getModel()->getDestinationSubdir()) );
        }
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $originalUrl = parent::__toString();
        $product= $this->getProduct();
        $documentId = Mage::helper('web2print')->getItemId($product->getWeb2printDocumentId());

        // is it a chili document and is the preview allowed?
        if (!($this->chiliPreviewAllowed() && $documentId)) {
            return $originalUrl;
        }

        // if product image is uploaded in backend, show original image
        if ($product->getData($this->_getModel()->getDestinationSubdir()) != "no_selection" && $documentId) {
            return $originalUrl;
        }

        // is the image cached?
        if ($this->getCacheImage($documentId)) {
            return $this->getCacheImage($documentId);
        }

        // redirect to the chili-generated image:
        $url = $this->_getChiliImageUrl($documentId);
        return $url;
    }

    /**
     * Returns a list of all available image conversion profiles
     * @todo get this information from this file: /Chili/Web2print/Model/System/Config/Source/Imagelocation.php
     */
    public function getImageConversionProfileLocations()
    {
        return array('category', 'product_detail', 'cart', 'order', 'concept');
    }

    /**
     *
     * @param type $documentId
     * @param type $productId
     * @param type $type
     * @return string 
     */
    public function getImageByOption($documentId, $productId = null, $type = 'default')
    {
        $product = Mage::getModel('catalog/product')->load($productId);
        $documentId = $documentId ? $documentId : Mage::helper('web2print')->getItemId($product->getWeb2printDocumentId());

        // if product image is uploaded in backend, show original image
        if ($product->getData($this->_getModel()->getDestinationSubdir()) != "no_selection") {
            return false;
        }

        // special cases for chili preview images:
        if ($documentId) {
            $cache = $this->getCacheImage($documentId);
            if ($cache) {
                return $cache;
            }
            return $this->_getChiliImageUrl($documentId);
        }

        // fallback cases:
        return Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/image.jpg');
    }

    /**
     * Retrieve Original image size as array
     * 0 - width, 1 - height
     *
     * @return array
     */
    public function getOriginalSizeArray()
    {
        // try to catch chili document
        $product= $this->getProduct();
        $documentId = Mage::helper('web2print')->getItemId($product->getWeb2printDocumentId());

        if ($documentId && $this->chiliPreviewAllowed()) {
            $cached = $this->getCacheImage($documentId);
            if (!$cached) {
                $cached = $this->generateCachedImage($documentId);
            }
            $this->_getModel()->setBaseFile( $cached );

            // try to fetch the image size from chili:
            $info = $this->getRemoteImageSize($cached);
            if ($info) {
                return array_slice($info, 0, 2);
            }
        }

        return parent::getOriginalSizeArray();
    }

    /**
     * Get an image from chili and save it to the cache
     *
     * @param $documentId
     * @return string
     */
    public function generateCachedImage($documentId, $locationProfile = null)
    {
        $conversionProfile = $this->getImageConversionProfile($locationProfile);
        $webservice = Mage::getModel('web2print/api');
        $documentUrl = $webservice->getResourceImageUrl($documentId, $conversionProfile);

        if($documentUrl){
            if(is_numeric(Mage::helper('web2print')->getCacheLifetimeImages()) && Mage::app()->getCacheInstance()->canUse('chili_images')){
                $cacheKey = 'image_'.$conversionProfile.'_'.$documentId;
                $cacheModel = Mage::getSingleton('core/cache');
                $cacheModel->save($documentUrl, $cacheKey, array("chili_images"), Mage::helper('web2print')->getCacheLifetimeImages());

                Mage::helper('web2print/data')->log('Saving in image cache with key: '.$cacheKey. ' value: '.$documentUrl);
            }
        } else {
            Mage::helper('web2print/data')->log('Unable to get preview image for document ID '.$documentId);
        }

        return $documentUrl;
    }

    /**
     * Try to get the filesize of a remote file (CHILI)
     *
     * @param $url
     * @return array | false
     */
    public function getRemoteImageSize($url)
    {
        $context = array();

        // Download to temporary file:
        $localFile = tempnam(sys_get_temp_dir(), "RMT_IMG");
        $local = fopen($localFile, "w");
        $remoteContext = $context ? stream_context_create($context) : null;
        $remote = fopen($url, 'r', false, $remoteContext);

        // validate streams:
        if (!$local || !$remote) {
            return false;
        }

        // copy filedata:
        while  ($line = fread($remote, 1024)) {
            fwrite($local, $line);
        }

        // collect image information and clean up created files
        $data = getimagesize($localFile);
        fclose($remote);
        fclose($local);
        return $data;
    }

    /**
     *
     * @param type $documentId
     * @return type cached image url
     */
    private function getCacheImage($documentId){
        $imgProfile = $this->getImageConversionProfile();
        $cacheName = 'image_'.$imgProfile.'_'.$documentId;
        $cacheModel  = Mage::getSingleton('core/cache');
        $cacheImgUrl = $cacheModel->load($cacheName);

        if($cacheImgUrl) {
           return $cacheImgUrl;
        }else{
           return false;
        }
    }

    /**
     * Clears the image cache for a specific CHILI document for all profiles or a specified one
     * @param string $documentId
     * @param string $locationProfile
     */
    public function clearChiliDocumentImageCache($documentId, $locationProfile = null) {

        $cacheModel = Mage::getSingleton('core/cache');

        // If no location is specified all cached images for this document ID will be cleared
        if($locationProfile == null) {
            $locations = $this->getImageConversionProfileLocations();

            foreach($locations as $location) {
                $cacheKey = 'image_' . $location . '_' . $documentId;
                $cacheModel->remove($cacheKey);
                Mage::helper('web2print/data')->log('Clearing image cache by key: '.$cacheKey);
            }

        } else {
            $profile = $this->getImageConversionProfile($locationProfile);
            $cacheKey = 'image_' . $profile . '_' . $documentId;
            $cacheModel->remove($cacheKey);
            Mage::helper('web2print/data')->log('Clearing image cache by key: '.$cacheKey);
        }
    }

    /**
     *
     * @param type $controller
     * @return type string image profile
     */
    public function getImageConversionProfile($controller = false){
        $type = '';

        if($controller){
            $controllerName = $controller;
        }else{
            $controllerName = Mage::app()->getRequest()->getControllerName();
        }
       
        switch($controllerName){
            case 'category':
                if(Mage::helper('web2print')->getCategoryProfile()){
                    $type = Mage::helper('web2print')->getCategoryProfile();
                }               
            break;
            case 'product':
            case 'product_detail':
                if(Mage::helper('web2print')->getProductProfile()){
                     $type = Mage::helper('web2print')->getProductProfile();
                }               
            break;
            case 'cart':
                if(Mage::helper('web2print')->getCartProfile()){
                    $type = Mage::helper('web2print')->getCartProfile();
                }              
            break;
            case 'order':
                if(Mage::helper('web2print')->getOrderProfile()){
                     $type = Mage::helper('web2print')->getOrderProfile();
                }              
            break;
            case 'concept':
                if(Mage::helper('web2print')->getConceptProfile()){
                    $type = Mage::helper('web2print')->getConceptProfile();
                }
            break;
        }
        
        if($type == '' && Mage::helper('web2print')->getDefaultImageProfile()){
            $type = Mage::helper('web2print')->getDefaultImageProfile(); 
        } elseif($type == '') {
            $type = 'full'; // Make sure there is always a fallback
        }

        return $type;
    }

    /**
     * Get the chili preview image url
     *
     * @param $documentId
     * @return string
     */
    protected function _getChiliImageUrl($documentId)
    {
        $controller = Mage::app()->getRequest()->getControllerName();
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
     * Check
     * @param null $controllerName
     * @return bool
     */
    public function chiliPreviewAllowed($controllerName = null) {
        $controllerName = ($controllerName) ? $controllerName : Mage::app()->getRequest()->getControllerName();
        $allowConfig = explode(',', Mage::getStoreConfig('web2print/profiles/preview_location'));

        switch ($controllerName) {
            case 'category':
                $key = 'category';
                break;
            case 'product':
                $key = 'product_detail';

                // fallback for no model available ..
                if (!$this->_getModel()) {
                    break;
                }

                //For thumbnails under main img
                if($this->_getModel()->getDestinationSubdir() == 'thumbnail' && $this->getImageFile()){
                    return false;
                }

                //For popup when clicking on thumbnails under main img
                if(Mage::app()->getRequest()->getActionName() == 'gallery') {
                    return false;
                }

                break;
            case 'cart':
                $key = 'cart';
                break;
            case 'order':
                $key = 'order';
                break;
            case 'concept':
                $key = 'concept';
                break;
            default:
                $key = '';
                return true;
                break;
        }

        if(in_array($key, $allowConfig)) {
            return true;
        } else {
            return false;
        }
    }

}
