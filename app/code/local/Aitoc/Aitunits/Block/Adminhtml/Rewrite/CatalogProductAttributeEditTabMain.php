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
/* AITOC static rewrite inserts start */
/* $meta=%default,AdjustWare_Nav% */
if(Mage::helper('core')->isModuleEnabled('AdjustWare_Nav')){
    class Aitoc_Aitunits_Block_Adminhtml_Rewrite_CatalogProductAttributeEditTabMain_Aittmp extends AdjustWare_Nav_Block_Rewrite_AdminCatalogProductAttributeEditTabMain {} 
 }else{
    /* default extends start */
    class Aitoc_Aitunits_Block_Adminhtml_Rewrite_CatalogProductAttributeEditTabMain_Aittmp extends Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Main {}
    /* default extends end */
}

/* AITOC static rewrite inserts end */
class Aitoc_Aitunits_Block_Adminhtml_Rewrite_CatalogProductAttributeEditTabMain extends Aitoc_Aitunits_Block_Adminhtml_Rewrite_CatalogProductAttributeEditTabMain_Aittmp 
{
    
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        // set readonly to 'apply_to' element
        $attributeObject = $this->getAttributeObject();
        if ($attributeObject->getId()) {
            $form = $this->getForm();
            $disableAttributeFields = Mage::helper('eav')
                ->getAttributeLockedFields($attributeObject->getEntityType()
                ->getEntityTypeCode());
            if (isset($disableAttributeFields[$attributeObject->getAttributeCode()])) 
            {
                $reqField = 'apply_to'; 
                $isReqField = in_array($reqField,$disableAttributeFields[$attributeObject->getAttributeCode()]); 
                if ($isReqField && $elm = $form->getElement($reqField)) 
                {
                    $elm->setReadonly(1);
                    $elm->setDisabled(1);
                }
            }
        }
        return $this;
    }
    
}