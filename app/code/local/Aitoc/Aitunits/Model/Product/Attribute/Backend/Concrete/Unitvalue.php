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
class Aitoc_Aitunits_Model_Product_Attribute_Backend_Concrete_Unitvalue
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
        $sValue = trim($object->getData($attributeName));
        if($sValue == '')
        {
            return true;
        }
        $validator = Mage::getModel('aitunits/validate_number');
        $result = ctype_alpha($sValue);
        if($result == false)
        {
            $message = 'Please, enter alfabetical characters only.';
            $eavExc = new Mage_Eav_Model_Entity_Attribute_Exception(Mage::helper('aitunits')->__($message));
            $eavExc->setAttributeCode($attributeName);
            throw $eavExc;
        }
        return true;
    }
   
}