<?php

class Chili_Web2print_Block_System_Entity_Form_Element_Resourcebrowser extends Varien_Data_Form_Element_Select
{
    public function getElementHtml()
    {
        $resourceInput = new Chili_Web2print_Block_System_Entity_Form_Element_Resourcebrowser_Render($this->getData());
        $resourceInput->setLayout(Mage::app()->getLayout());

        if (Mage::registry('current_product')){  
           if($resourceInput->getNote()){
            $resourceInput->setData('resource_type', $resourceInput->getNote()); 
           }
           $resourceInput->setData('name', 'product['.$resourceInput->getName().']');
        } else if (Mage::registry('current_category')) {
           if($resourceInput->getNote()){
            $resourceInput->setData('resource_type', $resourceInput->getNote()); 
           }
           $resourceInput->setData('name', 'general['.$resourceInput->getName().']');
        }

        $html = '';
        $html .= $resourceInput->toHtml();

        $html.= $this->getAfterElementHtml();
        return $html;
    }
}