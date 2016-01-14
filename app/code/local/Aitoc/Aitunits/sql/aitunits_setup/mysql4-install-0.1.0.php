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

//add product attributes
$catalogSetup = Mage::getResourceModel('catalog/setup', 'catalog_setup');

$catalogSetup->addAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_select_form',
    array(
        'group'         => 'Product Units and Quantities Options',
        'backend'       => 'aitunits/product_attribute_backend_universal',
        'frontend'      => '',
        'label'         => 'Replace QTY input with',
        'input'         => 'select',
        'class'         => '',
        'source'        => 'aitunits/product_attribute_source_selector_type',
        'global'        => true,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '',
        'apply_to'      => 'simple,virtual,configurable',
        'input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_selectform',
        'is_configurable'  => 0,
        'visible_on_front' => false,
        'used_in_product_listing' => true,
        'is_used_for_price_rules' => false
    )
);

//$catalogSetup->addAttribute(
//    Mage_Catalog_Model_Product::ENTITY, 'aitunits_allowed_qty_input',
//    array(
//        'group'         => 'Product Units and Quantities Options',
//        'backend'       => 'aitunits/product_attribute_backend_universal',
//        'frontend'      => '',
//        'label'         => 'Use allowed quantities only',
//        'input'         => 'select',
//        'class'         => '',
//        'source'        => 'eav/entity_attribute_source_boolean',
//        'global'        => true,
//        'visible'       => true,
//        'required'      => false,
//        'user_defined'  => false,
//        'default'       => '',
//        'apply_to'      => 'simple,virtual,configurable',
//        'input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_allowedqtyinput',
//        'is_configurable'  => 0,
//        'visible_on_front' => false,
//        'used_in_product_listing' => true,
//        'is_used_for_price_rules' => false
//    )
//);

$catalogSetup->addAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_allowed_qty_values',
    array(
        'group'         => 'Product Units and Quantities Options',
        'backend'       => 'aitunits/product_attribute_backend_concrete_allowedqtyvalues',
        'frontend'      => '',
        'label'         => 'Use quantities',
        'input'         => 'text',
        'class'         => '',
        'source'        => '',
        'global'        => true,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '',
        'apply_to'      => 'simple,virtual,configurable',
        'input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_allowedqtyvalues',
        'is_configurable'  => 0,
        'visible_on_front' => false,
        'used_in_product_listing' => true,
        'is_used_for_price_rules' => false
    )
);

//$catalogSetup->addAttribute(
//    Mage_Catalog_Model_Product::ENTITY, 'aitunits_allowed_qty_beyond',
//    array(
//        'group'         => 'Product Units and Quantities Options',
//        'backend'       => 'aitunits/product_attribute_backend_universal',
//        'frontend'      => '',
//        'label'         => 'Allow customer to specify custom QTY other than specified QTY',
//        'input'         => 'select',
//        'class'         => '',
//        'source'        => 'eav/entity_attribute_source_boolean',
//        'global'        => true,
//        'visible'       => true,
//        'required'      => false,
//        'user_defined'  => false,
//        'default'       => '',
//        'apply_to'      => 'simple,virtual,configurable',
//        'input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_allowedqtybeyond',
//        'is_configurable'  => 0,
//        'visible_on_front' => false,
//        'used_in_product_listing' => true,
//        'is_used_for_price_rules' => false
//    )
//);

$catalogSetup->addAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_unit_enable',
    array(
        'group'         => 'Product Units and Quantities Options',
        'backend'       => 'aitunits/product_attribute_backend_universal',
        'frontend'      => '',
        'label'         => 'Allow units',
        'input'         => 'select',
        'class'         => '',
        'source'        => 'eav/entity_attribute_source_boolean',
        'global'        => true,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '',
        'apply_to'      => 'simple,virtual,configurable',
        'input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_unitenable',
        'is_configurable'  => 0,
        'visible_on_front' => false,
        'used_in_product_listing' => true,
        'is_used_for_price_rules' => false
    )
);

$catalogSetup->addAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_unit_value',
    array(
        'group'         => 'Product Units and Quantities Options',
        'backend'       => 'aitunits/product_attribute_backend_concrete_unitvalue',
        'frontend'      => '',
        'label'         => 'Price per',
        'input'         => 'text',
        'class'         => '',
        'source'        => '',
        'global'        => true,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '',
        'apply_to'      => 'simple,virtual,configurable',
        'input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_unitvalue',
        'is_configurable'  => 0,
        'visible_on_front' => false,
        'used_in_product_listing' => true,
        'is_used_for_price_rules' => false
    )
);

$catalogSetup->addAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_unit_divider',
    array(
        'group'         => 'Product Units and Quantities Options',
        'backend'       => 'aitunits/product_attribute_backend_universal',
        'frontend'      => '',
        'label'         => '"Price per" divider',
        'input'         => 'select',
        'class'         => '',
        'source'        => 'aitunits/product_attribute_source_divider',
        'global'        => true,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '',
        'apply_to'      => 'simple,virtual,configurable',
        'input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_unitdivider',
        'is_configurable'  => 0,
        'visible_on_front' => false,
        'used_in_product_listing' => true,
        'is_used_for_price_rules' => false
    )
);

$catalogSetup->addAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_instock_qty_show',
    array(
        'group'         => 'Product Units and Quantities Options',
        'backend'       => 'aitunits/product_attribute_backend_universal',
        'frontend'      => '',
        'label'         => 'Show product in stock quantity',
        'input'         => 'select',
        'class'         => '',
        'source'        => 'aitunits/product_attribute_source_stock_product_qty',
        'global'        => true,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '',
        'apply_to'      => 'simple,virtual,configurable',
        'input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_instockqtyshow',
        'is_configurable'  => 0,
        'visible_on_front' => false,
        'used_in_product_listing' => true,
        'is_used_for_price_rules' => false
    )
);

//$catalogSetup->addAttribute(
//    Mage_Catalog_Model_Product::ENTITY, 'aitunits_instock_qty_word_full',
//    array(
//        'group'         => 'Product Units and Quantities Options',
//        'backend'       => 'aitunits/product_attribute_backend_concrete_instockqtyword',
//        'frontend'      => '',
//        'label'         => 'Item availability: In Stock',
//        'input'         => 'text',
//        'class'         => '',
//        'source'        => '',
//        'global'        => true,
//        'visible'       => true,
//        'required'      => false,
//        'user_defined'  => false,
//        'default'       => '',
//        'apply_to'      => 'simple,virtual,configurable',
//        'input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_instockqtywordfull',
//        'is_configurable'  => 0,
//        'visible_on_front' => false,
//        'used_in_product_listing' => true,
//        'is_used_for_price_rules' => false
//    )
//);

$catalogSetup->addAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_instock_qty_word_high',
    array(
        'group'         => 'Product Units and Quantities Options',
        'backend'       => 'aitunits/product_attribute_backend_concrete_instockqtyword',
        'frontend'      => '',
        'label'         => 'Item availability: Sell out risk - Moderate',
        'input'         => 'text',
        'class'         => '',
        'source'        => '',
        'global'        => true,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '',
        'apply_to'      => 'simple,virtual,configurable',
        'input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_instockqtywordhigh',
        'is_configurable'  => 0,
        'visible_on_front' => false,
        'used_in_product_listing' => true,
        'is_used_for_price_rules' => false
    )
);

$catalogSetup->addAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_instock_qty_word_med',
    array(
        'group'         => 'Product Units and Quantities Options',
        'backend'       => 'aitunits/product_attribute_backend_concrete_instockqtyword',
        'frontend'      => '',
        'label'         => 'Item availability: Sell out risk - High',
        'input'         => 'text',
        'class'         => '',
        'source'        => '',
        'global'        => true,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '',
        'apply_to'      => 'simple,virtual,configurable',
        'input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_instockqtywordmed',
        'is_configurable'  => 0,
        'visible_on_front' => false,
        'used_in_product_listing' => true,
        'is_used_for_price_rules' => false
    )
);

$catalogSetup->addAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_instock_qty_word_low',
    array(
        'group'         => 'Product Units and Quantities Options',
        'backend'       => 'aitunits/product_attribute_backend_concrete_instockqtyword',
        'frontend'      => '',
        'label'         => 'Item availability: Hurry Up! - Just few in stock',
        'input'         => 'text',
        'class'         => '',
        'source'        => '',
        'global'        => true,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '',
        'apply_to'      => 'simple,virtual,configurable',
        'input_renderer'   => 'aitunits/adminhtml_catalog_product_config_form_field_instockqtywordlow',
        'is_configurable'  => 0,
        'visible_on_front' => false,
        'used_in_product_listing' => true,
        'is_used_for_price_rules' => false
    )
);

$installer->endSetup();