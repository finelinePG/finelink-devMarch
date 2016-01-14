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
abstract class Aitoc_Aitunits_Model_Validate_Number_Abstract extends Mage_Core_Model_Abstract 
{
    protected function _isValid($sValue)
    {
        //$regFloat = '/^([0-9]+)\.([0-9]+)$/';
        $regInt = '/^([0-9]+)$/';
        if(!preg_match($regInt, $sValue))//!preg_match($regFloat, $sValue) )
        {
            return false;    
        }
        return true;
    }
}