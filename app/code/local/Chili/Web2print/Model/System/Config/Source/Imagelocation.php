<?php
class Chili_Web2print_Model_System_Config_Source_Imagelocation
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'category', 'label'=>Mage::helper('web2print')->__('Category overview page')),
            array('value' => 'product_detail', 'label'=>Mage::helper('web2print')->__('Product detail page')),
            array('value' => 'cart', 'label'=>Mage::helper('web2print')->__('Shopping cart page')),
            array('value' => 'order', 'label'=>Mage::helper('web2print')->__('Order detail page')),
            array('value' => 'concept', 'label'=>Mage::helper('web2print')->__('Concepts overview page')),
        );
    }
}