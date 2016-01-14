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
class Aitoc_Aitunits_Model_Observer_Block_Replacer_Checkoutcart
    extends Aitoc_Aitunits_Model_Observer_Block_Replacer_Abstract
{
    
    protected $_requiredBlockType = 'checkout/cart';
    
    protected function _getAllowedRoutes()
    {
        return array(
            'aitcheckout_checkout_index',
            'aitcheckout_checkout_updateSteps',
            'aiteditablecart_cart_updatePost'
        );
    }
    
    protected function _getAdditionalHtml()
    {
        $block = Mage::getBlockSingleton('core/template');
        $block->setTemplate('aitunits/checkout/cart/aitcheckout/jsreplace.phtml');
        return $block->toHtml();
    }
    
}