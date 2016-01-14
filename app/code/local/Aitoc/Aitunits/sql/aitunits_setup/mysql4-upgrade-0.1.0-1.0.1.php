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

$catalogSetup->updateAttribute('catalog_product', 'aitunits_select_form', 'used_for_sort_by', 1);
$catalogSetup->updateAttribute('catalog_product', 'aitunits_allowed_qty_values', 'used_for_sort_by', 1);
$catalogSetup->updateAttribute('catalog_product', 'aitunits_unit_enable', 'used_for_sort_by', 1);
$catalogSetup->updateAttribute('catalog_product', 'aitunits_unit_value', 'used_for_sort_by', 1);
$catalogSetup->updateAttribute('catalog_product', 'aitunits_unit_divider', 'used_for_sort_by', 1);
$catalogSetup->updateAttribute('catalog_product', 'aitunits_instock_qty_show', 'used_for_sort_by', 1);
$catalogSetup->updateAttribute('catalog_product', 'aitunits_instock_qty_word_high', 'used_for_sort_by', 1);
$catalogSetup->updateAttribute('catalog_product', 'aitunits_instock_qty_word_med', 'used_for_sort_by', 1);
$catalogSetup->updateAttribute('catalog_product', 'aitunits_instock_qty_word_low', 'used_for_sort_by', 1);

$installer->endSetup();