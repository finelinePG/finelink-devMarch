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
class Aitoc_Aitunits_Model_Observer_Block_Replacer_Catalogproductprice 
    extends Aitoc_Aitunits_Model_Observer_Block_Replacer_Abstract 
{
    
    protected $_requiredBlockType = 'catalog/product_price';
    
    public function _init()
    {
        $this->_addMarkFilter();
    }
    
    protected function _getAllowedRoutes()
    {
        return array(
            'catalog_category_view',
            'catalogsearch_result_index',
            'tag_product_list',
            'catalogsearch_advanced_result',
            'adjnav_ajax_category',
            'adjnav_ajax_search',
            'adjgiftreg_index_popular',
            'aitmanufacturers_index_view',
        );
    }
    
    protected function _getAdditionalHtml()
    {
        $product = $this->_block->getProduct();
        $block = $this->_getSelectBlock(false,$product);
        $typeBlock = $block->getTypeBlock();
        if($typeBlock)
        {
            $typeBlock->addContainer();
        }
        $block->enableTypeBlockRender();
        return $block->toHtml();
    }

}