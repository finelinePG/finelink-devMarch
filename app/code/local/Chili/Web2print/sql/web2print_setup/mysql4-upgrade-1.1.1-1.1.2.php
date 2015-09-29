<?php
$installer = $this;

$installer->run("
      ALTER TABLE {$installer->getTable('web2print/pdf')} ADD `message` VARCHAR( 2048 ) NULL AFTER `status`
");

