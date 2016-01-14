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
class Aitoc_Aitcheckout_Helper_Onlyif_Data extends Aitoc_Aitcheckout_Helper_Abstract
{
    public function saveBilling($currentStep, $customerAddressId)
    {
        if($currentStep == 'payment' && Mage::helper('aitcheckout/aitconfcheckout')->isEnabled() && Mage::helper('customer')->isLoggedIn())
        {
            if (!Mage::getSingleton('checkout/type_onepage')->getQuote()->getBillingAddress()->getData('customer_address_id'))
            {
                if ($addId = Mage::app()->getRequest()->getPost('billing_address_id', false))
                {
                    $customerAddressId = $addId;
                }
                Mage::getSingleton('checkout/type_onepage')->saveBilling(Mage::app()->getRequest()->getPost('billing', array()), $customerAddressId);
            }
        }
    }
}