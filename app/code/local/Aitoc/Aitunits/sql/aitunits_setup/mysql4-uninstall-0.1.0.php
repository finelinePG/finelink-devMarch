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
        'backend_model'       => NULL,
        'source_model'        => NULL,
        'is_visible'       => false,
        'apply_to'      => '',
        'frontend_input_renderer'   => NULL,
        'used_in_product_listing' => false
    )
);

//$catalogSetup->updateAttribute(
//    Mage_Catalog_Model_Product::ENTITY, 'aitunits_allowed_qty_input',
//    array(
//        'backend_model'       => NULL,
//        'source_model'        => NULL,
//        'is_visible'       => false,
//        'apply_to'      => '',
//        'frontend_input_renderer'   => NULL,
//        'used_in_product_listing' => false
//    )
//);

$catalogSetup->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_allowed_qty_values',
    array(
        'backend_model'       => NULL,
        'source_model'        => NULL,
        'is_visible'       => false,
        'apply_to'      => '',
        'frontend_input_renderer'   => NULL,
        'used_in_product_listing' => false
    )
);

//$catalogSetup->updateAttribute(
//    Mage_Catalog_Model_Product::ENTITY, 'aitunits_allowed_qty_beyond',
//    array(
//        'backend_model'       => NULL,
//        'source_model'        => NULL,
//        'is_visible'       => false,
//        'apply_to'      => '',
//        'frontend_input_renderer'   => NULL,
//        'used_in_product_listing' => false
//    )
//);

$catalogSetup->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_unit_enable',
    array(
        'backend_model'       => NULL,
        'source_model'        => NULL,
        'is_visible'       => false,
        'apply_to'      => '',
        'frontend_input_renderer'   => NULL,
        'used_in_product_listing' => false
    )
);

$catalogSetup->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_unit_value',
    array(
        'backend_model'       => NULL,
        'source_model'        => NULL,
        'is_visible'       => false,
        'apply_to'      => '',
        'frontend_input_renderer'   => NULL,
        'used_in_product_listing' => false
    )
);

$catalogSetup->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_unit_divider',
    array(
        'backend_model'       => NULL,
        'source_model'        => NULL,
        'is_visible'       => false,
        'apply_to'      => '',
        'frontend_input_renderer'   => NULL,
        'used_in_product_listing' => false
    )
);

$catalogSetup->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_instock_qty_show',
    array(
        'backend_model'       => NULL,
        'source_model'        => NULL,
        'is_visible'       => false,
        'apply_to'      => '',
        'frontend_input_renderer'   => NULL,
        'used_in_product_listing' => false
    )
);

//$catalogSetup->updateAttribute(
//    Mage_Catalog_Model_Product::ENTITY, 'aitunits_instock_qty_word_full',
//    array(
//        'backend_model'       =>NULL,
//        'source_model'        => NULL,
//        'is_visible'       => false,
//        'apply_to'      => '',
//        'frontend_input_renderer'   => NULL,
//        'used_in_product_listing' => false
//    )
//);

$catalogSetup->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_instock_qty_word_high',
    array(
        'backend_model'       => NULL,
        'source_model'        => NULL,
        'is_visible'       => false,
        'apply_to'      => '',
        'frontend_input_renderer'   => NULL,
        'used_in_product_listing' => false
    )
);

$catalogSetup->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_instock_qty_word_med',
    array(
        'backend_model'       => NULL,
        'source_model'        => NULL,
        'is_visible'       => false,
        'apply_to'      => '',
        'frontend_input_renderer'   => NULL,
        'used_in_product_listing' => false
    )
);

$catalogSetup->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'aitunits_instock_qty_word_low',
    array(
        'backend_model'       => NULL,
        'source_model'        => NULL,
        'is_visible'       => false,
        'apply_to'      => '',
        'frontend_input_renderer'   => NULL,
        'used_in_product_listing' => false
    )
);

$installer->endSetup();