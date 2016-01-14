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
class Aitoc_Aitunits_Block_Adminhtml_Catalog_Product_Config_Form_Field_Allowedqtyvalues
    extends Aitoc_Aitunits_Block_Adminhtml_Catalog_Product_Config_Form_Field_Type_Text
{
    /**
     * Get config value data
     *
     * @return mixed
     */
    protected function _getValueFromConfig()
    {
        return Mage::getStoreConfig(Aitoc_Aitunits_Helper_Data::XPATH_CONFIG_AITUNITS_ALLOWED_QTY_VALUES);
    }
    
}