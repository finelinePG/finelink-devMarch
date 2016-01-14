<?php
/**
 * All-In-One Checkout v1.0.15 : All-In-One Checkout v1.0.15 (OPCB Unit)
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcheckout / Aitoc_Aitcheckout
 * @version      1.0.15 - 1.4.15
 * @license:     jC7sr77MwqoHj2SDR8w4YXR3o3w7irXLNFUdRYpgyc
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitcheckout_Block_Checkout_Shipping extends Aitoc_Aitcheckout_Block_Checkout_Step
{
    protected $_stepType = 'Shipping';
    
    protected $_configs = array();
    
    public function checkFieldShow($key)
    {
        $this->_configs = Mage::helper('aitconfcheckout/onepage')->initConfigs('shipping');
        return Mage::helper('aitconfcheckout/onepage')->checkFieldShow($key, $this->_configs);
    }
    
    public function isShow()
    {
        return !$this->getQuote()->isVirtual();    
    }
    
    public function getMethod()
    {
        return $this->getQuote()->getCheckoutMethod();
    }
    
    public function customerHasAddresses()
    {
        if (Mage::helper('aitcheckout/adjgiftregistry')->getGiftAddressId()){
            return true;
        }
        return parent::customerHasAddresses();
    }
}