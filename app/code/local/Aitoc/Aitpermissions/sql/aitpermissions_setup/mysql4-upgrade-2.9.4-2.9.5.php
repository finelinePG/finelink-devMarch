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

$installer->run($sql = "
ALTER TABLE `{$this->getTable('aitoc_aitpermissions_advancedrole')}` ADD `manage_orders_own_products_only` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;
");
$installer->endSetup();