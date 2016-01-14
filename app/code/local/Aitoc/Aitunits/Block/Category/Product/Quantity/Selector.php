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
class Aitoc_Aitunits_Block_Category_Product_Quantity_Selector extends Aitoc_Aitunits_Block_Category_Product_Decorator
{
    
    protected $_canJsRender = true;
    protected $_canTypeBlockRender = false;
    protected $_typeBlock;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('aitunits/product/quantity/selector.phtml');
    }
    
    public function canRender()
    {
        if(!Mage::helper('aitunits')->getAvailableStores())
        {
            return false;
        }
    
        /* {#AITOC_COMMENT_END#}
        $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('Aitoc_Aitunits')->getLicense()->getPerformer();
        $ruler = $performer->getRuler();
        $groupId = Mage::app()->getGroup()->getId();
        if(!in_array($groupId, $ruler->getAvailableStores()))
        {
            return false;
        }
        {#AITOC_COMMENT_START#} */
        $isValidProduct = Mage::helper('aitunits')->isUsableProduct($this->getProduct());
        $value = $this->getProductAttributeValue('aitunits_select_form');
        if(!empty($value) && $isValidProduct)
        {
            return true;
        }
        return false;
    }
    
    public function getTypeBlockHtml()
    {
        $block = $this->getTypeBlock();
        return $block->renderView();
    }
    
    public function getJsParams()
    {
        $params =  array(
            'selectType'    => $this->getProductAttributeValue('aitunits_select_form'),
            'allowedValues' => $this->getAllowedValues(),
            'id'            => $this->getId(),
            'selectorHtml'  => $this->getTypeBlockHtml(),
            'itemId'        => $this->getItemId(),
            //'useOnlyAllowed'=> $this->getProductAttributeValue('aitunits_allowed_qty_input'),
        );
        return Mage::helper('core')->jsonEncode($params);
    }
    
    public function getAllowedValues()
    {
        return Mage::helper('aitunits')->getAllowedQtys($this->getProduct());
    }
    
    public function disableJsRender()
    {
        $this->_canJsRender = false;
        return $this;
    }
    
    public function canJsRender()
    {
        return $this->_canJsRender;
    }
    
    public function enableTypeBlockRender()
    {
        $this->_canTypeBlockRender = true;
        return $this;
    }
    
    public function canTypeBlockRender()
    {
        return $this->_canTypeBlockRender;
    }
    
    public function getTypeBlock()
    {
        $typeBlock = $this->_typeBlock;
        if(!$typeBlock)
        {
            $selectorType = $this->getProductAttributeValue('aitunits_select_form');
            if($selectorType)
            {
                $typeBlock = $this->getHelper('aitunits/category_product_quantity_selector_type_'.$selectorType);
                if(!($typeBlock instanceof Aitoc_Aitunits_Block_Category_Product_Quantity_Selector_Type_Abstract))
                {
                    return false;
                }
                if(isset($this->_item))
                {
                    $typeBlock->setItem($this->getItem());
                }
                else
                {
                    $typeBlock->setProduct($this->getProduct());
                }
                $this->_typeBlock = $typeBlock;
            } 
        }
        return $typeBlock;
    }
    
    protected function _afterToHtml($html)
    {
        $this->_typeBlock = null;
        return $html;
    }
    
    public function initBlock($context)
    {
        switch($context)
        {
            case('catalog_product_view'):
                $this->setProduct(Mage::helper('catalog')->getProduct());
                if($this->getTypeBlock())
                {
                    $this->getTypeBlock()->addContainer();   
                }
            default:
                return;
        }
    }

}