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
class Aitoc_Aitpermissions_Block_Rewrite_AdminhtmlCatalogFormRendererFieldsetElement
    extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    protected $_disableAlways = array('created_by');

    public function checkFieldDisable()
    {
        $result = parent::checkFieldDisable();
        $role = Mage::getSingleton('aitpermissions/role');
        $element = $this->getElement();

        if(!($element && $element->getEntityAttribute()) || !$role->isPermissionsEnabled())
        {
            return $result;
        }

        if (in_array($element->getEntityAttribute()->getAttributeCode(), $this->_disableAlways))
        {
            Mage::helper('aitpermissions')->disableElement($element);
            return $result;
        }

        if ($this->getRequest()->getActionName() == 'new')
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

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = parent::render($element);
        if ($this->getElement() && 
            $this->getElement()->getEntityAttribute() &&
            $this->getElement()->getEntityAttribute()->isScopeGlobal())
        {
            $role = Mage::getSingleton('aitpermissions/role');

            if ($role->isPermissionsEnabled() &&
                !$role->canEditGlobalAttributes() &&
                ('msrp' == $this->getElement()->getHtmlId()))
            {
                 $html .= '
                    <script type="text/javascript">
                    //<![CDATA[
                    if (Prototype.Browser.IE)
                    {
                        if (window.addEventListener)
                        {
                            window.addEventListener("load", aitpermissions_disable_msrp, false);
                        }
                        else
                        {
                            window.attachEvent("onload", aitpermissions_disable_msrp);
                        }
                    }
                    else
                    {
                        document.observe("dom:loaded", aitpermissions_disable_msrp);
                    }

                    function aitpermissions_disable_msrp()
                    {
                        ["click", "focus", "change"].each(function(evt){
                            var msrp = $("msrp");
                            if (msrp && !msrp.disabled)
                            {
                                Event.observe(msrp, evt, function(el) {
                                    el.disabled = true;
                                }.curry(msrp));
                            }
                        });
                    }
                    //]]>
                    </script>';
            }

            if (!$role->canEditGlobalAttributes())
            {
                $html = str_replace(
                    '<script type="text/javascript">toggleValueElements(',
                    '<script type="text/javascript">//toggleValueElements(',
                    $html
                );
            }
        }
        
        return $html;
    }
}