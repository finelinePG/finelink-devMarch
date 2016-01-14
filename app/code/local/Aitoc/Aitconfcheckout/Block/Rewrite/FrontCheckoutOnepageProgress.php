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
class Aitoc_Aitconfcheckout_Block_Rewrite_FrontCheckoutOnepageProgress extends Mage_Checkout_Block_Onepage_Progress
{
    public function getBilling()
    {
    // start aitoc
        return Mage::helper('aitconfcheckout/onepage')->getAddress(parent::getBilling());
    // finish aitoc        
    // return $this->getQuote()->getBillingAddress();
    }

    public function getShipping()
    {
    // start aitoc
        return Mage::helper('aitconfcheckout/onepage')->getAddress(parent::getShipping());
    // finish aitoc        
    // return $this->getQuote()->getShippingAddress();
    }

    public function checkStepActive($sStepCode)
    {
        return Mage::helper('aitconfcheckout')->checkStepActive($this->getQuote(), $sStepCode);
    }

    public function getProcessAddressHtml($sHtml)
    {
        $sHtml = nl2br($sHtml);

        $sHtml = str_replace(array('<br/>','<br />'), array('<br>', '<br>'), $sHtml); 
        
        $aReplace = array
        (
'<br><br>',    
    
'<br>
<br>',        

', <br>', ',  <br>'        
        );       
        
        while (strpos($sHtml, $aReplace[0]) !== false OR strpos($sHtml, $aReplace[1]) !== false) 
        {
        	$sHtml = str_replace($aReplace, '<br>', $sHtml);
        }

        if (strpos($sHtml, '<br>') === 0)
        {
            $sHtml = substr($sHtml, 4);
        }
           
        return $sHtml;
    }      
    
}