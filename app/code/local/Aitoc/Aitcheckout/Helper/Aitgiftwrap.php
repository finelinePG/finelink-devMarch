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
class Aitoc_Aitcheckout_Helper_Aitgiftwrap extends Aitoc_Aitcheckout_Helper_Abstract
{
    protected $_isEnabled = null;
    
    /**
     * Check whether the GR module is active or not
     * 
     * @return boolean
     */
    public function isEnabled()
    {
        if($this->_isEnabled === null)
        {
            $this->_isEnabled = ($this->isModuleEnabled('Aitoc_Aitgiftwrap') && Mage::app()->getLayout()->createBlock('aitgiftwrap/giftwrap_onepage')->isShow())?true:false;
        }
        return $this->_isEnabled;
    }
}