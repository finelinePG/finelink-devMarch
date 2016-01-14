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
class Aitoc_Aitunits_Model_Observer_Block_Replacer_Checkoutcartitemrenderer 
    extends Aitoc_Aitunits_Model_Observer_Block_Replacer_Abstract 
{
    
    protected $_requiredBlockType = 'checkout/cart_item_renderer';
    
    protected function _getAllowedRoutes()
    {
        return array(
            'checkout_cart_index',
            'aitcheckout_checkout_index',
            'aitcheckout_checkout_updateSteps',
            'aiteditablecart_cart_updatePost'
        );
    }
    
    protected function _getAdditionalHtml()
    {
        $item = $this->_block->getItem();
        $block = $this->_getSelectBlock($item,false);
        $typeBlock = $block->getTypeBlock();
        if($typeBlock)
        {
            $typeBlock->addContainer();
        }
        return $block->toHtml();
    }
    
    protected function _isRequiredBlock()
    {
        $result = parent::_isRequiredBlock();
        //for Aitcheckout module compatibility
        if($result == true)
        {
            $renderedBlock = $this->_block->getRenderedBlock();
            if(!isset($renderedBlock))
            {
                return true;
            }
            $blockName = $renderedBlock->getNameInLayout();
            $route = $this->_getRoute();
            if(isset($blockName) && !empty($blockName) && $route == 'aitcheckout_checkout_updateSteps'&& $blockName == 'aitcheckout.checkout.review.info')
            {
                return false;
            }
            if(isset($blockName) && !empty($blockName) && $route == 'checkout_cart_index'&& $blockName!== 'checkout.cart')
            {
                return false;
            }
            return true;
        }
        //--
        return false;
    }

}