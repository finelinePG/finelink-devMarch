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
class Aitoc_Aitcheckout_Block_Checkout_Review extends Mage_Checkout_Block_Onepage_Review
{
    /**
     *
     * @return Aitoc_Autcheckout_Helper_Sagepay
     */
    public function getDefaultHelper()
    {
        return Mage::helper('aitcheckout/sagepay');
    }

    public function isSagePayFormPaymentModeActive()
    {
        $post = $this->getRequest()->getPost();
        return isset($post['payment']['method']) && ('sagepayform' == $post['payment']['method']);
    }

    public function getReviewUrl()
    {
        
        if ($this->getDefaultHelper()->checkIfEbizmartsSagePaySuiteFormModeActiveOnly() && $this->isSagePayFormPaymentModeActive())
        {
            return $this->getUrl('sgps/payment/onepageSaveOrder', array('_secure'=>true));
        }
        else
        {
            return $this->getUrl('aitcheckout/checkout/saveOrder', array('form_key' => Mage::getSingleton('core/session')->getFormKey(), '_secure'=>true));
        }
    }
    
    /**
     * Validate if order amount is allowed to purchase
     *
     * @return boolean
     */
    public function isDisabled()
    {
        return Mage::helper('aitcheckout')->isPlaceOrderDisabled();
    }
    
	  /**
     * @return boolean
     */
    public function isSaveOrderAction()
    {
        return (Mage::app()->getRequest()->getActionName() == 'saveOrder');
    }
  
}