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
class Aitoc_Aitunits_Model_System_Config_Source_Selector_Type
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>Mage::helper('aitunits')->__('Off')),
            array('value' => 'dropdown', 'label'=>Mage::helper('aitunits')->__('Dropdown')),
            array('value' => 'slider', 'label'=>Mage::helper('aitunits')->__('Slider')),
            array('value' => 'plus', 'label'=>Mage::helper('aitunits')->__('Plus Minus')),
            array('value' => 'arrows', 'label'=>Mage::helper('aitunits')->__('Arrows')),
        );
    }

}