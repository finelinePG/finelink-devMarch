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
class Aitoc_Aitcheckout_Helper_Login extends Aitoc_Aitcheckout_Helper_Abstract
{

    /**
     * @return boolean
     */
    private function _isCheckoutLoginPersistent()
	{
        return Mage::getConfig()->getModuleConfig('Mage_Persistent')->is('active', 'true');
    }
	
	/**
     * Return login block tempates. There are no persistent template in old versions of magento.
     *
     * @return string
     */
    public function getLoginTemplatePath()
	{
        if($this->_isCheckoutLoginPersistent()){
            return "persistent/checkout/onepage/login.phtml";
        }
        return  "checkout/onepage/login.phtml";
    }

}