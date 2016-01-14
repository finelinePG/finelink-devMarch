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
class Aitoc_Aitcheckout_Block_Rewrite_PaypalPayflowAdvancedIframe extends Mage_Paypal_Block_Payflow_Advanced_Iframe
{

    public function setTemplate($template)
    {
        if (($template == 'paypal/payflowadvanced/redirect.phtml') && !Mage::helper('aitcheckout')->isDisabled()) {
            $template = 'aitcheckout/paypal/payflowadvanced/redirect.phtml';
        }
        return parent::setTemplate($template);
    }
    
    public function getAitCheckoutRedirectUrl()
    {
        if (Mage::helper('aitcheckout')->isShowCheckoutInCart()) {
            $url = $this->getUrl(Mage::helper('aitcheckout')->getCartUrl());
        } else {
            $url = $this->getUrl(Mage::helper('aitcheckout')->getCheckoutUrl());
        }
        return $url;
    }

}