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
class Aitoc_Aitunits_Model_Observer_Block_Replacer_Catalogproductviewtypegrouped 
    extends Aitoc_Aitunits_Model_Observer_Block_Replacer_Abstract 
{
    
    protected $_requiredBlockType = 'catalog/product_view_type_grouped';
    
    protected function _getAllowedRoutes()
    {
        return array(
            'catalog_product_view',
            'wishlist_index_configure',
        );
    }
    
    protected function _getAdditionalHtml()
    {
        $aAssociatedProducts = $this->_block->getAssociatedProducts();
        $html = null;
        $suffix = null;
        foreach ($aAssociatedProducts as $product)
        {
            $block = $this->_getSelectBlock(false,$product);
            $block->setSuffix($suffix);
            $typeBlock = $block->getTypeBlock();
            if($typeBlock)
            {
                $typeBlock->setSuffix($suffix);
                $typeBlock->addContainer();
            }
            $html .= $block->toHtml();
        }
        return $html;
    }
}