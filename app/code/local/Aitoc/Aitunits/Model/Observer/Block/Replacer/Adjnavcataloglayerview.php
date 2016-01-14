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
class Aitoc_Aitunits_Model_Observer_Block_Replacer_Adjnavcataloglayerview
    extends Aitoc_Aitunits_Model_Observer_Block_Replacer_Abstract
{
    
    protected $_requiredBlockType = 'adjnav/catalog_layer_view';
    
    protected function _getAllowedRoutes()
    {
        return array(
            'catalog_category_view',
            'catalogsearch_result_index',
            'aitmanufacturers_index_view',
        );
    }
    
    protected function _getAdditionalHtml()
    {
        if(Mage::getVersion() >= '1.4.0.1')
        {
            $block = Mage::getBlockSingleton('aitunits/adjnav_layer_js');
            return $block->toHtml();
        }
    }
}