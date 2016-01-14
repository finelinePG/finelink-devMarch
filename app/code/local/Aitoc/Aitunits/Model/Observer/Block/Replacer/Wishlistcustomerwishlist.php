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
class Aitoc_Aitunits_Model_Observer_Block_Replacer_Wishlistcustomerwishlist
    extends Aitoc_Aitunits_Model_Observer_Block_Replacer_Abstract 
{
    
    protected $_requiredBlockType = 'wishlist/customer_wishlist';
    
    protected function _getAllowedRoutes()
    {
        return array(
            'wishlist_index_index',
        );
    }
    
    protected function _getAdditionalHtml()
    {
        $aItems = $this->_block->getWishlistItems();
        $html = null;
        foreach ($aItems as $item)
        {
            $block = $this->_getSelectBlock($item,false);
            $typeBlock = $block->getTypeBlock();
            if($typeBlock)
            {
                $typeBlock->addContainer();
            }
            $html .= $block->toHtml();
        }
        return $html;
    }
}