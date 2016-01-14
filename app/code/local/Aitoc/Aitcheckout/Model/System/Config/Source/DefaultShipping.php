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
class Aitoc_Aitcheckout_Model_System_Config_Source_DefaultShipping
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $methods = array(array('value'=>'','label'=>Mage::helper('adminhtml')->__('No default shipping method')));

        $activeCarriers = Mage::getSingleton('shipping/config')->getActiveCarriers();
        foreach($activeCarriers as $carrierCode => $carrierModel)
        {
            $options = array();
            if( $carrierMethods = $carrierModel->getAllowedMethods() )
            {
                foreach ($carrierMethods as $methodCode => $method)
                {
                    $code= $carrierCode.'_'.$methodCode;
                    $options[]=array('value'=>$code,'label'=>$method);

                }
                $carrierTitle = Mage::getStoreConfig('carriers/'.$carrierCode.'/title');

            }
            $methods[]=array('value'=>$options,'label'=>$carrierTitle);
        }
        return $methods;
    }

}