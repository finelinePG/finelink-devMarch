<?php

class Chili_Web2print_Block_Javascript extends Mage_Core_Block_Template 
{

	public $_product;

	public function __construct() {
		$this->_product = Mage::getModel('catalog/product')->load( $this->getRequest()->getParam('id'));
	}
	
	
	/**
	 * @return	string	environment for the CHILI connection 
	 */
	public function getChiliEnvironment() {
		return Mage::getStoreConfig('web2print/connection/environment');
	}


	/**
	 * @return	string	username for the CHILI connection 
	 */
	public function getChiliUsername() {
		return Mage::getStoreConfig('web2print/connection/username');
	}

	
	/**
	 * @return	string	password for the CHILI connection 
	 */
	public function getChiliPassword() {
		return Mage::getStoreConfig('web2print/connection/password');
	}

	/**
	 * Add document.domain = '...' to the javascript file for cross domain editing
	 * @return	string	
	 */
	public function getDocumentDomain() {
		$return = '';	
		if( Mage::helper( 'web2print' )->getChiliDocumentDomain()) {
			$return = 'document.domain = "' . Mage::helper( 'web2print' )->getChiliDocumentDomain() . '";';
		}
		return $return;
	}
	
	public function getDocumentId() {
		return $this->_product->getWeb2printDocumentId();
	}
	
	public function getOptionId() {
		return Mage::getStoreConfig('web2print/general/custom_option');
	}
	
	public function getWeb2printUrl() {
		if( !$this->getDocumentId()) {
			return false;
		}
		return Mage::getModel('web2print/api')->getDocumentUrl($this->getDocumentId());
	}
        
        public function getAjaxUrl($path){
            if(Mage::app()->getStore()->isCurrentlySecure()){
                //return 'secure';
                return Mage::app()->getStore(Mage::app()->getStore()->getId())->getUrl($path, array('_forced_secure'=>true)); 
            }else{
                return Mage::app()->getStore(Mage::app()->getStore()->getId())->getUrl($path);
            }
        }
}