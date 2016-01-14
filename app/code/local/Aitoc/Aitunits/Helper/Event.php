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
class Aitoc_Aitunits_Helper_Event extends Mage_Core_Helper_Abstract
{
    
    public function addAfterToHtml($html, Mage_Core_Block_Abstract $block)
    {
        $transportObject = new Varien_Object;
        $transportObject->setHtml($html);
        Mage::dispatchEvent('aitunits_core_block_template_to_html_after',
            array('block' => $block, 'transport' => $transportObject));
        $html = $transportObject->getHtml();
        return $html;
    }
    
}