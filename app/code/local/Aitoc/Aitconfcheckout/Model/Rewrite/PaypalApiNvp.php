<?php
/**
 * All-In-One Checkout v1.0.15 : All-In-One Checkout v1.0.15 (CC Unit)
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcheckout / Aitoc_Aitconfcheckout
 * @version      1.0.15 - 2.1.29
 * @license:     jC7sr77MwqoHj2SDR8w4YXR3o3w7irXLNFUdRYpgyc
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitconfcheckout_Model_Rewrite_PaypalApiNvp extends Mage_Paypal_Model_Api_Nvp
{
    public function callDoExpressCheckoutPayment()
    {
        if(
            !Mage::getStoreConfig('aitconfcheckout/shipping/active') ||
            !Mage::getStoreConfig('aitconfcheckout/shipping/address') ||
            !Mage::getStoreConfig('aitconfcheckout/shipping/city') ||
            !Mage::getStoreConfig('aitconfcheckout/shipping/region') ||
            !Mage::getStoreConfig('aitconfcheckout/shipping/country') ||
            !Mage::getStoreConfig('aitconfcheckout/shipping/postcode') ||
            !Mage::getStoreConfig('aitconfcheckout/shipping/telephone')
          )
        {
            $this->setSuppressShipping(true);
        }

        parent::callDoExpressCheckoutPayment();
    }
}