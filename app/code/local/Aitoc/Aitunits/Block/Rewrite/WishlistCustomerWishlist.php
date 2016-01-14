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
class Aitoc_Aitunits_Block_Rewrite_WishlistCustomerWishlist extends Mage_Wishlist_Block_Customer_Wishlist
{
    
    protected function _toHtml()
    {
        $this->setType('wishlist/customer_wishlist');
        $html = parent::_toHtml();
        if(Mage::getVersion() > '1.4.2.0')
        {
            $html = Mage::helper('aitunits/event')->addAfterToHtml($html,$this);
        }
        return $html;
    }
}