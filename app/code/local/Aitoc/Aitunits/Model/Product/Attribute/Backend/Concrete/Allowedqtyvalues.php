<?php
/**
 * Product Units and Quantities
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitunits
 * @version      1.0.11
 * @license:     0JdTQfDMswel7fbpH42tkXIHe3fixI4GH3daX0aDVf
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2012 AITOC, Inc. 
 */
class Aitoc_Aitunits_Model_Product_Attribute_Backend_Concrete_Allowedqtyvalues
	extends Aitoc_Aitunits_Model_Product_Attribute_Backend_Abstract
{
    
    public function validate($object)
    {
        $attributeName = $this->getAttribute()->getName();
        $sUseConfig = $object->getData('use_config_'.$attributeName);
        if($sUseConfig == '1')
        {
            return true;
        }
        $sValues = trim($object->getData($attributeName));
        if($sValues=='')
        {
            return true;
        }
        $sValues = str_replace(' ', '', $sValues);
        $validator = Mage::getModel('aitunits/validate_number_list');
        $result = $validator->validate($sValues);
        if($result == false)
        {
            $message = 'Please, enter correct numbers.';
            $eavExc = new Mage_Eav_Model_Entity_Attribute_Exception(Mage::helper('aitunits')->__($message));
            $eavExc->setAttributeCode($attributeName);
            throw $eavExc;
        }
        return true;
    }
   
}