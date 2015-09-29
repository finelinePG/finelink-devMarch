<?php
$installer = $this;
$installer->startSetup();

/*creates a database where the concept products are stored*/
$installer->run("
    DROP TABLE IF EXISTS {$installer->getTable('web2print/concept')};
    CREATE TABLE IF NOT EXISTS {$installer->getTable('web2print/concept')} (
        `concept_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `customer_id` int(11) DEFAULT NULL,
        `product_id` int(11) DEFAULT NULL,
        `store_id` int(11) DEFAULT NULL,
        `chili_id` varchar(255) NOT NULL DEFAULT '',
        `description` varchar(255) NOT NULL DEFAULT '',
        `options` text NOT NULL,
        `status` text NOT NULL,
        PRIMARY KEY (`concept_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
");

$installer->endSetup();