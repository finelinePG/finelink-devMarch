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
class Aitoc_Aitconfcheckout_Helper_Paypal extends Mage_Core_Helper_Abstract
{
    protected $configs = array();

    public function __construct()
    {
        foreach(array('billing','shipping') as $sType)
        {
            if(!isset($this->_configs[$sType]))
            {
                $this->_configs[$sType] = array();
            }

            $aAllowedFieldHash = Mage::helper('aitconfcheckout')->getAllowedFieldHash($sType);

            foreach ($aAllowedFieldHash as $sKey => $bValue)
            {
                $this->_configs[$sType][$sKey] = $bValue;
            }
        }

    }

    public function checkFieldShow($sType,$sKey)
    {
        if (!$sKey || !isset($this->_configs[$sType]) || !isset($this->_configs[$sType][$sKey]))
        {
            return false;
        }

        if ($this->_configs[$sType][$sKey])
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}