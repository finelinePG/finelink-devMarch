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
if(Mage::getVersion() <= '1.4.2.0')
{
    class Aitoc_Aitunits_Block_Adminhtml_Catalog_Product_Config_Form_Helper extends Varien_Data_Form_Element_Select
    {
        /**
        * Retrieve element html
        *
        * @return string
        */
        public function getElementHtml()
        {
            $value = $this->getValue();
            if ($value == '') {
                $this->setValue($this->_getValueFromConfig());
            }
            $html = parent::getElementHtml();

            $htmlId   = 'use_config_' . $this->getHtmlId();
            $checked  = ($value == '') ? ' checked="checked"' : '';
            $disabled = ($this->getReadonly()) ? ' disabled="disabled"' : '';

            $html .= '<input id="'.$htmlId.'" name="product['.$htmlId.']" '.$disabled.' value="1" ' . $checked;
            $html .= ' onclick="toggleValueElements(this, this.parentNode);" class="checkbox" type="checkbox" />';
            $html .= ' <label for="'.$htmlId.'">' . Mage::helper('adminhtml')->__('Use Config Settings').'</label>';
            $html .= '<script type="text/javascript">toggleValueElements($(\''.$htmlId.'\'), $(\''.$htmlId.'\').parentNode);</script>';

            return $html;
        }

        /**
        * Get config value data
        *
        * @return mixed
        */
        protected function _getValueFromConfig()
        {
            return '';
        }
    }
}
else
{
    class Aitoc_Aitunits_Block_Adminhtml_Catalog_Product_Config_Form_Helper 
        extends Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Config
    {
        
    }
}