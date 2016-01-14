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
class Aitoc_Aitcheckout_Block_Checkout_Login extends Mage_Checkout_Block_Onepage_Abstract
{
    public function getCaptchaReloadUrl() {
        if(!$this->helper('aitcheckout/captcha')->checkIfCaptchaEnabled()) {
            return '';
        }
        $blockPath = Mage::helper('captcha')->getCaptcha('user_login')->getBlockName();
        $block = $this->getLayout()->createBlock($blockPath);
        return $block->getRefreshUrl();
    }

}