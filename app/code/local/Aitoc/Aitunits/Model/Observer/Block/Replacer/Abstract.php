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
abstract class Aitoc_Aitunits_Model_Observer_Block_Replacer_Abstract
    extends Aitoc_Aitunits_Model_Observer_Abstract
{
    
    protected $_block = null;
    protected $_requiredBlockType;

    public function replace(Varien_Event_Observer $observer)
    {
        //$r = $this->_getRoute();
        if( !in_array( $this->_getRoute() , $this->_getAllowedRoutes()) )
        {
            return ;
        }
        $this->_initEvent($observer);
        $this->_init();
        
        $this->_block = $observer->getBlock();
        
        if(!$this->_isRequiredBlock())
        {
            return;
        }
        
        if(!$this->_checkInProductMark())
        {
            return; 
        }
       
        $transport = $observer->getTransport();
        $html = $transport->getHtml();
        $html .= $this->_getAdditionalHtml();
        $transport->setHtml($html);
    }
    
    abstract protected function _getAdditionalHtml();
    
    protected function _getSelectBlock($item, $product)
    {
        $block = Mage::getBlockSingleton('aitunits/category_product_quantity_selector');
        if($item)
        {
            $block->setItem($item);
        }
        else
        {
            $block->setProduct($product);
        }
        return $block;
    }
    
    protected function _isRequiredBlock()
    {
        if($this->_block->getType() == $this->_requiredBlockType)
        {
            if(!($this->_block instanceof Mage_Core_Block_Template))
            {
                Mage::throwException('Aitunits :: invalid block type');
            }
            return true;
        }
        return false;
    }
    
}