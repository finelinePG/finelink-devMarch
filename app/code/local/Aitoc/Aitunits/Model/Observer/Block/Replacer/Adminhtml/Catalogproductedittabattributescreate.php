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
class Aitoc_Aitunits_Model_Observer_Block_Replacer_Adminhtml_Catalogproductedittabattributescreate
    extends Aitoc_Aitunits_Model_Observer_Abstract
{
    
    protected function _getAllowedRoutes()
    {
        return array(
            'adminhtml_catalog_product_edit',
            'adminhtml_catalog_product_new',
        );
    }
    
    public function replace($observer)
    {
        if( !in_array( $this->_getRoute() , $this->_getAllowedRoutes()) )
        {
            return ;
        }
        $this->_initEvent($observer);

        $block = $this->_getEvent()->getBlock();
        $group = Mage::getModel('eav/entity_attribute_group')->load($block->getConfig()->getGroupId());
        $reqGroupName = Mage::helper('aitunits')->getAttributeGroupName();
        if($group->getAttributeGroupName() == $reqGroupName)
        {
            $block->setCanShow(false);
        }
    }
    
}