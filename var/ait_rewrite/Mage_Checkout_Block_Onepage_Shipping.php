<?php
/* DO NOT MODIFY THIS FILE! THIS IS TEMPORARY FILE AND WILL BE RE-GENERATED AS SOON AS CACHE CLEARED. */

class Aitoc_Aitcheckoutfields_Block_Rewrite_FrontCheckoutOnepageShipping extends Mage_Checkout_Block_Onepage_Shipping
{
    
    protected function _construct()
    {
        parent::_construct();
    }
    
    public function getFieldHtml($aField)
    {
        $sSetName = 'shipping';
        
        return Mage::getModel('aitcheckoutfields/aitcheckoutfields')->getAttributeHtml($aField, $sSetName, 'onepage');
    }
    
    public function getCustomFieldList($iTplPlaceId)
    {
        $iStepId = Mage::helper('aitcheckoutfields')->getStepId('shippinfo');
        
        if (!$iStepId) return false;

        return Mage::getModel('aitcheckoutfields/aitcheckoutfields')->getCheckoutAttributeList($iStepId, $iTplPlaceId, 'onepage');
    } 
}


class Aitoc_Aitconfcheckout_Block_Rewrite_FrontCheckoutOnepageShipping extends Aitoc_Aitcheckoutfields_Block_Rewrite_FrontCheckoutOnepageShipping
{    
    protected $_configs = array();
        
    protected function _construct()
    {
        $this->_configs = Mage::helper('aitconfcheckout/onepage')->initConfigs('shipping');
        parent::_construct();
    }
    
    public function getDefaultCountryId()
    {
        return Mage::helper('aitconfcheckout')->getDefaultCountryId();
    }    
    
    public function checkFieldShow($key)
    {
        return Mage::helper('aitconfcheckout/onepage')->checkFieldShow($key, $this->_configs);
    }
    
    public function checkStepActive($sStepCode)
    {
        return Mage::helper('aitconfcheckout')->checkStepActive($this->getQuote(), $sStepCode);
    }

    public function getDisabledSectionHash()
    {
        return Mage::getModel('aitconfcheckout/aitconfcheckout')->getDisabledSectionHash($this->getQuote());
    }   
    
	public function checkSkipShippingAllowed()
    {
        return Mage::helper('aitconfcheckout/onepage')->checkSkipShippingAllowed();
    }
	
    // override parent
    public function getAddressesHtmlSelect($type)
    {
	    return Mage::helper('aitconfcheckout/onepage')->getAddressesHtmlSelect(parent::getAddressesHtmlSelect($type));
    }    
       
    // override parent
    function getAddress() 
	{        
        return Mage::helper('aitconfcheckout/onepage')->getAddress(parent::getAddress());
    }    
}

