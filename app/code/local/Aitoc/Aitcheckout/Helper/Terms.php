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
class Aitoc_Aitcheckout_Helper_Terms extends Aitoc_Aitcheckout_Helper_Abstract
{
    /*
     * Terms and conditions display mode
     */
    public function getTocMode()
    {
        return Mage::getStoreConfig('checkout/aitcheckout/conditions_mode');
    }

    /*
     * Terms and conditions popup width
     */
    public function getTocPopupWidth()
    {
        return Mage::getStoreConfig('checkout/aitcheckout/popup_width');
    }

    /*
     * Terms and conditions popup height
     */
    public function getTocPopupHeight()
    {
        return Mage::getStoreConfig('checkout/aitcheckout/popup_height');
    }

    /*
     * Terms and conditions checkbox behavior
     */
    public function getTocCheckboxBehavior()
    {
        return Mage::getStoreConfig('checkout/aitcheckout/checkbox_behavior');
    }

}