<?php
/**
 * Advanced Permissions
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitpermissions
 * @version      2.10.9
 * @license:     bJ9U1uR7Gejdp32uEI9Z7xOqHZ5UnP25Ct3xHTMyeC
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitpermissions_Block_Rewrite_AdminhtmlCatalogFormRendererAttributeUrlkey
    extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Attribute_Urlkey
{

    public function checkFieldDisable()
    {
        $result = parent::checkFieldDisable();
        $role = Mage::getSingleton('aitpermissions/role');
        $element = $this->getElement();

        if(!($element && $element->getEntityAttribute()) || !$role->isPermissionsEnabled() || $this->getRequest()->getActionName() == 'new')
        {
            return $result;
        }

        $attributePermissionArray =  Mage::helper('aitpermissions')->getAttributePermission();
        if(isset($attributePermissionArray[$element->getEntityAttribute()->getAttributeId()]))
        {
            if($attributePermissionArray[$element->getEntityAttribute()->getAttributeId()] == 0)
            {
                Mage::helper('aitpermissions')->disableElement($element);
            }

            return $result;
        }

        if (($element->getEntityAttribute()->isScopeGlobal() && !$role->canEditGlobalAttributes())
            || ($element->getEntityAttribute()->isScopeWebsite() && $role->getScope() != 'website'))
        {
            Mage::helper('aitpermissions')->disableElement($element);
        }


        return $result;
    }
}