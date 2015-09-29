<?php
class Chili_Web2print_Block_System_Config_Form_Element_Resourcebrowser extends Mage_Adminhtml_Block_System_Config_Form_Field{
    const TEMPLATE_PATH = 'web2print/form/element/render/resourcebrowser.phtml';

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate(self::TEMPLATE_PATH);

    }
    
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->addJs('web2print/resourcebrowser.js');
        $this->getLayout()->getBlock('head')->addCss('css/resourcebrowser.css');
        $this->getLayout()->getBlock('head')->addItem('js_css','prototype/windows/themes/default.css');
        if (Mage::helper('web2print')->getMagentoVersionForCss()) {
            $this->getLayout()->getBlock('head')->addCss('lib/prototype/windows/themes/magento.css');
        } else {
            $this->getLayout()->getBlock('head')->addItem('js_css','prototype/windows/themes/magento.css');
        }
        $block = $this->getLayout()->createBlock(
        'Chili_Web2print_Block_System_Config_Form_Resourcebrowserbase',
        'resourcebrowser_base'
        );

        $this->getLayout()->getBlock('content')->append($block);

        return parent::_prepareLayout();
    } 
    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
        $this->setData($element->getData());

        return $this->_toHtml();
    }
}