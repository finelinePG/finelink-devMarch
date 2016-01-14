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
 
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

//update product attributes
$catalogSetup = Mage::getResourceModel('catalog/setup', 'catalog_setup');


$catalogSetup->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_select_form',
    array(
        'backend_model'       => 'aitunits/product_attribute_backend_universal',
        'source_model'        => 'aitunits/product_attribute_source_selector_type',
        'is_visible'       => true,
        'apply_to'      => 'simple,virtual,configurable',
        'frontend_input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_selectform',
        'used_in_product_listing' => true
    )
);

//$catalogSetup->updateAttribute(
//    Mage_Catalog_Model_Product::ENTITY, 'aitunits_allowed_qty_input',
//    array(
//        'backend_model'       => 'aitunits/product_attribute_backend_universal',
//        'source_model'        => 'eav/entity_attribute_source_boolean',
//        'is_visible'       => true,
//        'apply_to'      => 'simple,virtual,configurable',
//        'frontend_input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_allowedqtyinput',
//        'used_in_product_listing' => true
//    )
//);

$catalogSetup->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_allowed_qty_values',
    array(
        'backend_model'       => 'aitunits/product_attribute_backend_concrete_allowedqtyvalues',
        'source_model'        => '',
        'is_visible'       => true,
        'apply_to'      => 'simple,virtual,configurable',
        'frontend_input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_allowedqtyvalues',
        'used_in_product_listing' => true
    )
);

//$catalogSetup->updateAttribute(
//    Mage_Catalog_Model_Product::ENTITY, 'aitunits_allowed_qty_beyond',
//    array(
//        'backend_model'       => 'aitunits/product_attribute_backend_universal',
//        'source_model'        => 'eav/entity_attribute_source_boolean',
//        'is_visible'       => true,
//        'apply_to'      => 'simple,virtual,configurable',
//        'frontend_input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_allowedqtybeyond',
//        'used_in_product_listing' => true
//    )
//);

$catalogSetup->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_unit_enable',
    array(
        'backend_model'       => 'aitunits/product_attribute_backend_universal',
        'source_model'        => 'eav/entity_attribute_source_boolean',
        'is_visible'       => true,
        'apply_to'      => 'simple,virtual,configurable',
        'frontend_input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_unitenable',
        'used_in_product_listing' => true
    )
);

$catalogSetup->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_unit_value',
    array(
        'backend_model'       => 'aitunits/product_attribute_backend_concrete_unitvalue',
        'source_model'        => '',
        'is_visible'       => true,
        'apply_to'      => 'simple,virtual,configurable',
        'frontend_input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_unitvalue',
        'used_in_product_listing' => true
    )
);

$catalogSetup->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_unit_divider',
    array(
        'backend_model'       => 'aitunits/product_attribute_backend_universal',
        'source_model'        => 'aitunits/product_attribute_source_divider',
        'is_visible'       => true,
        'apply_to'      => 'simple,virtual,configurable',
        'frontend_input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_unitdivider',
        'used_in_product_listing' => true
    )
);

$catalogSetup->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_instock_qty_show',
    array(
        'backend_model'       => 'aitunits/product_attribute_backend_universal',
        'source_model'        => 'aitunits/product_attribute_source_stock_product_qty',
        'is_visible'       => true,
        'apply_to'      => 'simple,virtual,configurable',
        'frontend_input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_instockqtyshow',
        'used_in_product_listing' => true
    )
);

//$catalogSetup->updateAttribute(
//    Mage_Catalog_Model_Product::ENTITY, 'aitunits_instock_qty_word_full',
//    array(
//        'backend_model'       => 'aitunits/product_attribute_backend_concrete_instockqtyword',
//        'source_model'        => '',
//        'is_visible'       => true,
//        'apply_to'      => 'simple,virtual,configurable',
//        'frontend_input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_instockqtywordfull',
//        'used_in_product_listing' => true
//    )
//);

$catalogSetup->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_instock_qty_word_high',
    array(
        'backend_model'       => 'aitunits/product_attribute_backend_concrete_instockqtyword',
        'source_model'        => '',
        'is_visible'       => true,
        'apply_to'      => 'simple,virtual,configurable',
        'frontend_input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_instockqtywordhigh',
        'used_in_product_listing' => true
    )
);

$catalogSetup->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_instock_qty_word_med',
    array(
        'backend_model'       => 'aitunits/product_attribute_backend_concrete_instockqtyword',
        'source_model'        => '',
        'is_visible'       => true,
        'apply_to'      => 'simple,virtual,configurable',
        'frontend_input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_instockqtywordmed',
        'used_in_product_listing' => true
    )
);

$catalogSetup->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_instock_qty_word_low',
    array(
        'backend_model'       => 'aitunits/product_attribute_backend_concrete_instockqtyword',
        'source_model'        => '',
        'is_visible'       => true,
        'apply_to'      => 'simple,virtual,configurable',
        'frontend_input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_instockqtywordlow',
        'used_in_product_listing' => true
    )
);

$installer->endSetup();