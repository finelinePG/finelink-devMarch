<?php

class Chili_Web2print_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * returns CHILI enabled
	 * @returns string enabled
	 */
	public function getChiliEnabled() {
        return Mage::getStoreConfig('web2print/general/enabled');
	}
	
	/**
	 * returns CHILI cache enabled
	 * @returns int enabled
	 */
	public function getChiliCaching() {
        return Mage::getStoreConfig('web2print/general/cache');
	}

	/**
	 * returns environment url
	 * @return string EnvironmentUrl
	 */
	public function getChiliEnvironment($website = null) {
        if($website){
            return $website->getConfig('web2print/connection/environment');
        }else{
            return Mage::getStoreConfig('web2print/connection/environment');
        }
	}

	/**
	 * returns environment domain
	 * @return string EnvironmentDomain
	 */
	public function getChiliDomain($website = null) {
        if($website && $website->getId()){
            return $website->getConfig('web2print/connection/domain');
        }else{
            return Mage::getStoreConfig('web2print/connection/domain');
        }
	}
        
    /**
    * returns environment wsdl
    * @returns string EnvironmentWsdl
    */
    public function getChiliWsdl($website = null) {
        return $this->getChiliDomain($website) . '/main.asmx?wsdl';
    }

    /**
     * Returns environment webservice URL
     * @return string EnvironmentWsdl
     */
    public function getChiliWebserviceUrl($website = null) {
        return $this->getChiliDomain($website) . '/main.asmx';
    }
    
	/**
	 * returns CHILI username
	 * @return string CHILIUsername
	 */
	public function getChiliUsername($website = null) {
        if($website){
            return $website->getConfig('web2print/connection/username');
        }else{
            return Mage::getStoreConfig('web2print/connection/username');
        }
	}

	/**
	 * returns CHILI password
	 * @return string CHILIPassword
	 */
	public function getChiliPassword($website = null) {
        if($website){
            return $website->getConfig('web2print/connection/password');
        }else{
            return Mage::getStoreConfig('web2print/connection/password');
        }
	}

    /*
     * get cache lifetime images
     */
    public function getCacheLifetimeImages() {
        return Mage::getStoreConfig('web2print/profiles/cache_lifetime_images');
    }

    /*
     * get cache lifetime editor
     */
    public function getCacheLifetimeEditor() {
        return Mage::getStoreConfig('web2print/editor_page/cache_lifetime_editor');
    }

    /**
     * returns the path to save to on the CHILI server
     * @param string    quote, order item or concept
     * @param array     multidimensional array with keys that will be replace by values
     * @return string
     */
    public function getChiliSavePath( $type = 'quote', $replaces = array()) {
        //check that locaqtions are filled
        $pathFilled = true;
        if(Mage::getStoreConfig('web2print/savepath/default_location') =='') {
            $pathFilled = false;
        }elseif(Mage::getStoreConfig('web2print/savepath/temp_location') =='') {
            $pathFilled = false;
        }elseif(Mage::getStoreConfig('web2print/savepath/final_location') =='') {
            $pathFilled = false;
        }

        if(!$pathFilled){
            Mage::getSingleton('checkout/session')->addError('Save path is not configured in backend');
            throw new Exception('Save path is not configured in backend');
        }

        // default path for any 'open' quote item
        $path = Mage::getStoreConfig('web2print/savepath/default_location');
        $replacers = array(
            '%year%'              => date('Y'),
            '%month%'             => date('n'),
            '%day%'               => date('j'),
            '%order_id%'          => '',
            '%quote_id%'          => '',
            '%product_id%'        => '',
            '%customer_group%'    => '',
        );

        if( $type == 'temp' ) {
            $path = Mage::getStoreConfig('web2print/savepath/temp_location');
        } elseif( $type == 'concept' ) {
            $path = Mage::getStoreConfig('web2print/savepath/concept_location');
        } elseif( $type == 'order' ) {
            $path = Mage::getStoreConfig('web2print/savepath/final_location');
        } else {
            $path = Mage::getStoreConfig('web2print/savepath/default_location');
        }

        $replacers  = array_merge( $replaces, $replacers );
        $path       = str_replace( array_keys( $replacers ), array_values( $replacers ), $path );

        return $path;
    }
        
	/**
	 * Extra logging functionality
	 */
	public function log($message, $loglevel= Zend_Log::DEBUG) {
        if ($this->debugEnabled()) {
            Mage::log( $message, $loglevel, $this->getLogFile());
        }
        return true;
	}
        
    /*
     * returns boolean
     */
    public function debugEnabled() {
        return Mage::getStoreConfig('web2print/general/debug');
	}
        
    /*
     * returns boolean
     */
    public function getLogFile() {
        return Mage::getStoreConfig('web2print/general/logfile');
	}

    /**
     * @todo get this working based on configuration
     * @return bool
     */
    public function getIsConceptEnabled()
    {
        return Mage::getStoreConfig('web2print/editor_page/concepts_enabled');
    }
        
    /*
     * returns string category profile name
     */
    public function getCategoryProfile() {
        $id = $this->getItemId(Mage::getStoreConfig('web2print/profiles/category'));
        return $id;
	}
        
    /*
     * returns string product's detail page profile name
     */
    public function getProductProfile() {
        $id = $this->getItemId(Mage::getStoreConfig('web2print/profiles/product'));
        return $id;
	}
        
    /*
     * returns string cart page profile name
     */
    public function getCartProfile() {
        $id = $this->getItemId(Mage::getStoreConfig('web2print/profiles/cart'));
        return $id;
	}
        
    /*
    * returns string order page profile name
    */
    public function getOrderProfile() {
        $id = $this->getItemId(Mage::getStoreConfig('web2print/profiles/order'));
        return $id;
    }

    /*
    * returns string concept overview page profile name
    */
    public function getConceptProfile() {
        $id = $this->getItemId(Mage::getStoreConfig('web2print/profiles/concept'));
        return $id;
    }

    /*
     * returns default profile name
     */
    public function getDefaultImageProfile(){
        $id = $this->getItemId(Mage::getStoreConfig('web2print/profiles/default'));
        return $id;
    }

    /* get name of pdf export setting */
    /* by order store id */
    public function getPDFSettingsName($default = 'frontend',  $product = null, $storeId = null){

        $attribute = 'web2print_'.$default.'_pdf_profile';
        if($product && $product->getData($attribute)){
            $id = $this->getItemId($product->getData($attribute));
        }else{
            if($storeId){
                $id = $this->getItemId(Mage::getStoreConfig('web2print/pdf/' . $default . '_pdf_profile', $storeId));
            }else{
                $id = $this->getItemId(Mage::getStoreConfig('web2print/pdf/' . $default . '_pdf_profile'));
            }
        }

        return $id;
    }

    public function getPdfExportProfile($type, $website = null, $productId = null, $storeId = null){
        $attribute = 'web2print_'.$type.'_pdf_profile';

        if($productId){
            $exportProfile = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, $attribute, $storeId);

            if($exportProfile) {
                return $exportProfile;
            }
        }

        if($website){
            return $website->getConfig('web2print/pdf/' . $type . '_pdf_profile');
        }else{
            return Mage::getStoreConfig('web2print/pdf/' . $type . '_pdf_profile');
        }

    }

    /* get the magento pdf save path*/
    public function getPDFSavePath($default = 'frontend',$website = null){
        if($website){
            $path = $website->getConfig('web2print/pdf/' . $default . '_pdf_path');
        }else{
            $path = Mage::getStoreConfig('web2print/pdf/' . $default . '_pdf_path');
        }

        if($path){
            return $path;
        }else{
            return 'media/pdf/'.$default.'/';
        }
    }

    /**
     * @param string $type configuration parameter value
     * @return string item ID
     */
    public function getItemId($type){
        $data = explode('|', $type);
        $index = count($data) - 1;
        return $data[$index];
    }

	/**
	 * is add to cart allowed wihout editing
	 * @returns int
	 */
	public function getChiliAllowAddToCartWithoutEditing() {
        return Mage::getStoreConfig('web2print/general/allow_add');
	}

	/**
	 * get width of the modal box
	 * @returns int
	 */
	public function getChiliIframeInversedHeight() {
        $configHeight = Mage::getStoreConfig('web2print/editor_page/iframe_inversed_height');
        return (( $configHeight )? $configHeight: 200 );
	}

    /*
     * get rights workspace
     */
    public function allowSimulateWorkspace(){
        if(Mage::getStoreConfig('web2print/editor_page/simulate_workspace')){
            return true;
        }else{
            return false;
        }
    }
	
	/**
	 * get workspace preference
	 */
	public function getCurrentWorkspacePreference( $productId = false )
    {
		if( $productId ) { 
			if( $productWorspacePreference = $this->getProductPreference( $productId, 'workspace_preference' )) {
				return $this->getItemId( $productWorspacePreference );
			} else if( $categoryWorkspacePreference = $this->getCategoryPreference( $productId, 'workspace_preference', true )) {
				return $this->getItemId( $categoryWorkspacePreference );
			}
		}
		
		if( $workspacePreference = Mage::getStoreConfig('web2print/editor_page/workspace_preference')) {
			return $this->getItemId( $workspacePreference );
		} 
		return false;
	}

	/**
	 * get view preference
	 */
	public function getCurrentViewPreference( $productId = false ) {
		if( $productId ) { 
			if( $productWorspacePreference = $this->getProductPreference( $productId, 'view_preference' )) {
				return $this->getItemId( $productWorspacePreference );
			} else if( $categoryViewPreference = $this->getCategoryPreference( $productId, 'view_preference', true )) {
				return $this->getItemId( $categoryViewPreference );
			}
		}
		if( $viewPreference = Mage::getStoreConfig('web2print/editor_page/view_preference')) {
			return $this->getItemId( $viewPreference );
		}
		return false;
	}

	/**
	 * get document constraints
	 */
	public function getCurrentDocumentConstraint( $productId = false ) {
		if( $productId ) { 
			if( $productWorspacePreference = $this->getProductPreference( $productId, 'document_constraint' )) {
				return $this->getItemId( $productWorspacePreference );
			} else if( $categoryDocumentConstraint = $this->getCategoryPreference( $productId, 'document_constraint', true )) {
				return $this->getItemId( $categoryDocumentConstraint );
			}
		}
		if( $documentConstraint = Mage::getStoreConfig('web2print/editor_page/document_constraint')) {
			return $this->getItemId( $documentConstraint );
		}
		return false;
	}

	/**
	 * if it's set: get the preference (view, workspace or document constraint) for a product
	 * @param	int		product id
	 * @param	string	
	 * @return 	string
	 */
	public function getProductPreference( $productId, $type ) {
		$product = Mage::getModel('catalog/product')->load( $productId );
		return $product->getData('web2print_' . $type );
	}

	/**
	 * if it's set: get the preference (view, workspace or document constraint) for a product
	 * @param	int		product id
	 * @param	string	
	 * @return 	string
	 */
	public function getCategoryPreference( $productId, $type ) {
		// load product
		$product 	= Mage::getModel('catalog/product')->load( $productId );
		
		// get current category and preference
		if( Mage::registry( 'current_category' ) && $preference = $this->getActiveCategoryPreference( $type )) {
			return $preference;
		} 

		// default
		$categories = $product->getCategoryIds();
		$category 	= Mage::getModel( 'catalog/category' )->load( array_shift( $categories ));
		return $category->getData( 'web2print_' . $type );
	}

	/**
	 * recursive function for the active preferences on category level
	 * @param	string 	preference type
	 * @return	string
	 */
	private function getActiveCategoryPreference( $type ) {
		$return = '';
		$current_category = Mage::registry( 'current_category' );
		while( $current_category->getData('level' ) >= 2 && $return == '' ) {
			$return = $current_category->getData( 'web2print_' . $type );
			$current_category = Mage::getModel( 'catalog/category' )->load( $current_category['parent_id'] );
		}
		return $return;
	}

    /**
     * Returns the document id saved in the quote item
     * @param int $quoteItemId 
     * @return string $documentId
     */
    public function getDocumentIdByQuoteItemId($quoteItemId){            
    	$quoteItem = Mage::getModel('sales/quote_item')->load($quoteItemId);
        $quoteItem = Mage::getModel('sales/quote')->load($quoteItem->getQuoteId())->getItemById($quoteItemId);
        $option = $quoteItem->getOptionByCode('chili_document_id');

        if($option == null){
            return null;
        }else{
            return $option->getValue();
        }
    }

    /**
     * Returns the document id saved in the quote item
     * @param int $quoteItemId
     * @return string $documentId
     */
    public function getDocumentIdByOrderItemId($orderItemId){
        $orderItem = Mage::getModel('sales/order_item')->load($orderItemId);
        $orderItem = Mage::getModel('sales/order')->load($orderItem->getOrderId())->getItemById($orderItemId);
        $option = $orderItem->getProductOptionByCode('chili_document_id');

        return $option;
    }

    /*
     * Returns the cart confirmation setting
     * 
     */
    public function getCartConfirmationSetting(){
        $isConfirmationEnabled = Mage::getStoreConfig('web2print/editor_page/cart_confirmation', Mage::app()->getStore()->getId());
        return $isConfirmationEnabled;        
    }
    
    /**
     * Compares webservice configuration from default vs website
     * @param Mage_Core_Model_Website $website
     * @return boolean 
     */
    public function isSameAsDefaultConfig($website){
        if($this->getChiliDomain() != $this->getChiliDomain($website)){
            return false;
        }
        if($this->getChiliEnvironment()!=$this->getChiliEnvironment($website)){
            return false;
        }
        if($this->getChiliUsername()!=$this->getChiliUsername($website)){
            return false;
        }
        if($this->getChiliPassword()!=$this->getChiliPassword($website)){
            return false;
        }
        return true;
    }
    
    public function isValidExportType($website,$exportType){
        if(! $this->isSameAsDefaultConfig($website)){
            if(Mage::getStoreConfig('web2print/pdf/' . $exportType . '_pdf_path') == $website->getConfig('web2print/pdf/' . $exportType . '_pdf_path')){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }
    
    public function getMagentoVersionForCss() {
        /* Do not use Mage::getEdition() for compatability reasons.
         * Function does not exist prior to Magento CE 1.7 and EE 1.12
         * and crashes Magento if used.
         */
        if (file_exists('LICENSE_EE.txt')) {
            $edition = 'Enterprise';
        } elseif (file_exists('LICENSE_PRO.html')) {
            $edition = 'Professional';
        } else {
            $edition = 'Community';
        }
        
        $communityVersion = ($edition == 'Community' && version_compare(Mage::getVersion(), '1.7.0.0', '>=')) ? true : false;
        $enterpriseVersion = ($edition == 'Enterprise' && version_compare(Mage::getVersion(), '1.12.0.0', '>=')) ? true : false;
        
        return ($communityVersion || $enterpriseVersion);
    }

    /**
     * @param $product
     * @param Mage_Catalog_Block_Product_View $block
     * @param array $additional
     * @return mixed
     */
    public function getAddToCartUrl($product, $block, $additional = array()) {
        if($product->getWeb2printDocumentId() == ''){
            return $block->getAddToCartUrl($product, $additional);
        }else{
            switch($product->getTypeId()){
                case 'configurable':
                case 'bundle':
                    return $product->getProductUrl();
                break;
                default:
                    $params = array( 'type' => 'product', 'id' => $product->getId() );
                    return Mage::getUrl('web2print/editor/load/', $params );
                break;
            }
        }
    }

    /**
     * @todo get this working based on input parameters
     * @return bool
     */
    public function isProductAllowed($product)
    {
        return $product->isSaleable();
    }

    /**
     * Get path to HighRes or LowRes PDF for product. Returns false if file does not exist yet.
     *
     * @param $documentId
     * @param $exportType
     *
     * @return bool|string
     */
    public function getPdfPath($documentId, $exportType)
    {
        if ($documentId) {
            $pdf = Mage::getModel('web2print/pdf')->getCollection()
                ->addFieldToFilter('document_id', $documentId)
                ->addFieldToFilter('export_type', $exportType)
                ->getFirstItem();
            if($pdf) {
                if($pdf->checkIfFileExists($pdf->getPath())){
                    return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$pdf->getPath();
                }
            }
        }
        return false;
    }
}

