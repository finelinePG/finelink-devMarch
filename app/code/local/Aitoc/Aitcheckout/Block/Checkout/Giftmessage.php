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
class Aitoc_Aitcheckout_Block_Checkout_Giftmessage extends Mage_GiftMessage_Block_Message_Inline
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('aitcheckout/giftmessage/inline.phtml');  
//        $this->setId('giftmessage_form_0')
//             ->setDontDisplayContainer(false)
//             ->setEntity(Mage::getSingleton('checkout/session')->getQuote());  
    }
    
    protected function _beforeToHtml()
    {
        $this->setId('giftmessage_form_0')
             ->setDontDisplayContainer(false)
             ->setEntity(Mage::getSingleton('checkout/session')->getQuote())  
             ->setType('onepage_checkout');   
    }

    public function isShow()
    {
        return (Mage::getStoreConfigFlag(Mage_GiftMessage_Helper_Message::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS) ||
                Mage::getStoreConfigFlag(Mage_GiftMessage_Helper_Message::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ORDER));
    } 
    
    public function isMessagesAvailable()
    {
        if (Aitoc_Aitsys_Abstract_Service::get()->isMagentoVersion('>=1.4.2'))
        {
            return parent::isMessagesAvailable();
        }
        else { 
            return Mage::helper('giftmessage/message')->isMessagesAvailable('quote', $this->getEntity());
        }
    }
       
    
}