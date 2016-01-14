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
if (version_compare(Mage::getVersion(), '1.7.0.0', '<'))
{
    class Aitoc_Aitcheckout_Block_Captcha extends Mage_Core_Block_Template
    {
    
    }
}
else
{
    class Aitoc_Aitcheckout_Block_Captcha extends Mage_Captcha_Block_Captcha
    {
    
        protected function _prepareLayout()
        {
            $headBlock = $this->getLayout()->getBlock('head');
			if($headBlock)
			{
				$headBlock->addJs('mage/captcha.js');
			}
			
            return parent::_prepareLayout();
        }    
    
    }
}