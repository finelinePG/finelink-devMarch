<?php
$installer = $this;
$installer->startSetup();

/*creates a database where the pdf urls are stored*/
$installer->run("
    DROP TABLE IF EXISTS {$installer->getTable('web2print/log')};
    CREATE TABLE IF NOT EXISTS {$installer->getTable('web2print/log')} (
        `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Log Id',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Time',
        `method` varchar(255) DEFAULT NULL COMMENT 'Method',
        `parameters` text COMMENT 'Parameters',
        PRIMARY KEY (`log_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='web2print_log' AUTO_INCREMENT=1 ;
");

$installer->endSetup();
