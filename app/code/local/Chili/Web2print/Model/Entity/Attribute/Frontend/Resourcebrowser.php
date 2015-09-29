<?php

class Chili_Web2print_Model_Entity_Attribute_Frontend_Resourcebrowser extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    /**
     *
     * @return type resource input render class
     */
    public function getInputRendererClass() {   
        $this->getAttribute()->setData('frontend_input_renderer', 'web2print/system_entity_form_element_resourcebrowser');
        return parent::getInputRendererClass();
    }

}