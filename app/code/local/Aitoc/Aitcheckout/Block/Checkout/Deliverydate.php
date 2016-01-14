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
class Aitoc_Aitcheckout_Block_Checkout_Deliverydate extends Mage_Checkout_Block_Onepage_Abstract
{
    protected $_show = null;

    public function isShow()
    {   
        if(is_null($this->_show))
        {
            $this->_show = $this->_checkIsShow();
        }
        return $this->_show;
    }

    protected function _checkIsShow()
    {
        if($this->helper('aitcheckout')->isModuleEnabled('AdjustWare_Deliverydate')
            && Mage::getStoreConfigFlag('checkout/adjdeliverydate/enabled'))
        {
            return Mage::helper('adjdeliverydate')->isShow();
        }

        return false;
    }

    protected function _toHtml()
    {
        if($this->isShow())
        {
            return $this->getLayout()
                ->createBlock('adjdeliverydate/container')
                ->setTemplate('aitcheckout/checkout/deliverydate.phtml')
                ->toHtml()
            ;
        }
        return '';
    }
}