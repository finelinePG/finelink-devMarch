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
$installer = $this;

$installer->startSetup();

$catalogSetup = Mage::getResourceModel('catalog/setup', 'catalog_setup');

$catalogSetup->updateAttribute('catalog_product', 'created_by', 'is_visible', '0'); 
$catalogSetup->updateAttribute('catalog_product', 'created_by', 'source_model', ''); 
$catalogSetup->updateAttribute('catalog_product', 'created_by', 'frontend_label', ''); 
$catalogSetup->updateAttribute('catalog_product', 'created_by', 'frontend_input', ''); 

$installer->endSetup();