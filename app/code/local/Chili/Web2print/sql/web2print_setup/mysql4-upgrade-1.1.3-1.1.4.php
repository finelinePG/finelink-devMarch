<?php
$installer = $this;

$installer->run("
  ALTER TABLE {$installer->getTable('web2print/pdf')} CHANGE `order_increment_id` `order_increment_id` VARCHAR( 50 ) NULL DEFAULT NULL;
  UPDATE {$installer->getTable('web2print/pdf')}  SET {$installer->getTable('web2print/pdf')}.order_increment_id = (SELECT increment_id FROM {$installer->getTable('sales_flat_order')} WHERE {$installer->getTable('sales_flat_order')}.entity_id = {$installer->getTable('web2print/pdf')}.order_id) WHERE order_increment_id = '2147483647';

");