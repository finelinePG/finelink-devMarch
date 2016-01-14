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
class Aitoc_Aitunits_Model_System_Config_Backend_Selector_Qty_Values 
    extends Aitoc_Aitunits_Model_System_Config_Backend_Selector_Qty_Abstract
{
    protected function _beforeSave()
    {
        $sValues = $this->getValue();
        $sValues = str_replace(' ', '', $sValues);
        $validator = Mage::getModel('aitunits/validate_number_list');
        $result = $validator->validate($sValues);
        
        if($result == false)
        {
            $label = $this->getFieldConfig()->label; 
            $message = 'Please enter correct numbers in " '.$label.' " field.';
            Mage::throwException(Mage::helper('aitunits')->__($message));
        }
        
        $this->setValue($sValues);
        return $this;
    }
}