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
class Aitoc_Aitunits_Model_Validate_Number_List extends Aitoc_Aitunits_Model_Validate_Number_Abstract
{
    public function validate($sValues)
    {
        $aValues = explode(',', $sValues);
        $iQty = count($aValues);
        if($iQty <= 1)
        {
            return false;
        }
        
        foreach($aValues as $sValue)
        {
            if(!$this->_isValid($sValue))
            {
                return false;
            }
        }
        return true;
    }
}