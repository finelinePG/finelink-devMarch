<?php
/**
 * All-In-One Checkout v1.0.15 : All-In-One Checkout v1.0.15 (CFM Unit)
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcheckout / Aitoc_Aitcheckoutfields
 * @version      1.0.15 - 2.9.15
 * @license:     jC7sr77MwqoHj2SDR8w4YXR3o3w7irXLNFUdRYpgyc
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
$installer = $this;

$installer->startSetup();

$tablesToCheck = array(
    'aitoc_order_entity_custom',
    'aitoc_custom_attribute_description',
    'aitoc_custom_attribute_need_select',
    'aitoc_customer_entity_data',
    'aitoc_custom_attribute_cg',
    'aitoc_custom_attribute_cat_refs',
    'aitoc_recurring_profile_entity_custom'
);
foreach($tablesToCheck as $table) {
    try {
        $installer->run("
            RENAME TABLE `".$table."` TO `".$installer->getTable($table)."` ;
        ");
    } catch(Exception $e)
    {
        //well, maybe tables were not exisits after all
    }
}

$installer->endSetup();