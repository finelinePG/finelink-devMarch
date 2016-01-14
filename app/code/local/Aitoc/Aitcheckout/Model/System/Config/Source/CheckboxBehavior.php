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
class Aitoc_Aitcheckout_Model_System_Config_Source_CheckboxBehavior
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => Aitoc_Aitcheckout_Helper_Data::CHECKBOX_AVAILABLE_INSTANTLY,   'label'=>Mage::helper('aitcheckout')->__('Mark the checkbox')),
            array('value' => Aitoc_Aitcheckout_Helper_Data::CHECKBOX_VIEWING_REQUIRED,      'label'=>Mage::helper('aitcheckout')->__('Display a pop-up window')),
        );
    }

}