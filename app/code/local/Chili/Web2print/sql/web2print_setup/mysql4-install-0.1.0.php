<?php

// inspiration
// http://blog.chapagain.com.np/magento-adding-attribute-from-mysql-setup-file/

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$entityTypeId = Mage::getModel('eav/entity')
                ->setType('catalog_product')
                ->getTypeId();

// add attribute group
$setup->addAttributeGroup('web2print', $entityTypeId, 'Chili Web2Print', 2);

// the attribute added will be displayed under the group/tab Special Attributes in product edit page
$setup->addAttribute($entityTypeId, 'web2print_document_id', array(
    'group'                         => 'Chili Web2print',
    'input'                         => 'text',
    'type'                          => 'text',
    'label'                         => 'Document Id',
    'backend'                       => '',
    'visible'                       => 1,
    'required'                      => 1,
    'user_defined'                  => 1,
    'searchable'                    => 1,
    'filterable'                    => 0,
    'comparable'                    => 0,
    'visible_on_front'              => 0,
    'visible_in_advanced_search'    => 1,
    'is_html_allowed_on_front'      => 0,
    'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'unique'                        => false,
    'apply_to'                      => 'simple,configurable,virtual,bundle,downloadable',
    'is_configurable'               => false,
    'frontend'                      => 'web2print/entity_attribute_frontend_resourcebrowser',
    'note'                          => 'Documents',
    'used_in_product_listing'       => 1
));

$setup->addAttribute($entityTypeId, 'web2print_allow_add', array(
    'group'         				=> 'Chili Web2print',
    'input'         				=> 'select',
    'type'          				=> 'int',
    'label'        					=> 'Allow add to cart without editing',
    'backend'       				=> '',
    'visible'       				=> 1,
    'required'      				=> 0,
    'user_defined' 					=> 1,
    'searchable' 					=> 0,
    'filterable' 					=> 0,
    'comparable'    				=> 0,
    'visible_on_front'	 			=> 0,
    'visible_in_advanced_search'    => 0,
    'is_html_allowed_on_front'      => 0,
    'global'        				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'unique'            			=> false,
    'apply_to'          			=> 'simple,configurable,virtual,bundle,downloadable',
    'is_configurable'   			=> false,
    'source'                        => 'eav/entity_attribute_source_boolean',
    'default'                       => Mage::helper('web2print')->getChiliAllowAddToCartWithoutEditing()
));

/*creates a database where the pdf urls are stored*/
$installer->run("
      DROP TABLE IF EXISTS {$installer->getTable('web2print/pdf')};
      CREATE  TABLE {$installer->getTable('web2print/pdf')}(
      `pdf_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `order_item_id` int(11) DEFAULT NULL,
      `order_id` int(11) DEFAULT NULL,
      `order_increment_id` int(11) DEFAULT NULL,      
      `path` varchar(255) DEFAULT NULL,
      `document_id` varchar(36) NOT NULL DEFAULT '',
      `task_id` varchar(36) NOT NULL DEFAULT '',
      `status` varchar(255) NULL DEFAULT '',
      `export_profile` varchar(255) NULL DEFAULT '',
      `export_type` varchar(255) NOT NULL DEFAULT 'frontend',
      `created_at` DATETIME NOT NULL,
      `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`pdf_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
  ");

$installer->endSetup();
