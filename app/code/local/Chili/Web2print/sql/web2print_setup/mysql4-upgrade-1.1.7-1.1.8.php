<?php
$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'web2print_enable_preview_pdf', array(
   'group'         		=> 'Chili Web2print',
   'input'         		=> 'boolean',
   'type'          		=> 'int',
   'label'        		=> 'Enable preview PDF download',
   'source'             => 'eav/entity_attribute_source_boolean',
   'visible'       		=> 1,
   'required'      		=> 0,
   'user_defined'       => 1,
   'global'        	    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
   'is_configurable'    => 0,
));

$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'web2print_enable_print_pdf', array(
   'group'         		=> 'Chili Web2print',
   'input'         		=> 'boolean',
   'type'          		=> 'int',
   'label'        		=> 'Enable print-ready PDF download',
   'source'             => 'eav/entity_attribute_source_boolean',
   'visible'       		=> 1,
   'required'      		=> 0,
   'user_defined'       => 1,
   'global'        	    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
   'is_configurable'    => 0,
));
$installer->endSetup();