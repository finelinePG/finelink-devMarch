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
class Aitoc_Aitcheckout_Block_Checkout_Paypal_Iframe extends Mage_Core_Block_Template
{
    public function isShow()
    {
        return Mage::helper('aitcheckout')->isPaypalAdvancedAvailable();
    }

    protected function _toHtml()
    {
        if($this->isShow())
        {
            $block = $this->getLayout()->createBlock('paypal/iframe', 'paypal.iframe');
            if (Mage::registry('aitcheckout_paypal_review_block_rendering')) {
                Mage::register('aitcheckout_paypal_iframe_block', $block);
            }
            $html = $block->toHtml();
               
            if ($html) {
                return $html . parent::_toHtml();
            }
        }
        return '';
    }
}