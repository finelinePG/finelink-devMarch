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
abstract class Aitoc_Aitunits_Model_Product_Attribute_Backend_Abstract
	extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
	/**
     * Set attribute default value if value empty
     *
     * @param Varien_Object $object
     * @return Mage_Catalog_Model_Product_Attribute_Backend_Boolean
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        if ($object->getData('use_config_' . $attributeCode)) 
        {
            $object->setData($attributeCode, '');
        }
        return $this;
    }
    
}