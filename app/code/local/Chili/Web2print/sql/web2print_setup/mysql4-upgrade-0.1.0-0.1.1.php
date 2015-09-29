<?php

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

/**
 * Add the custom product attributes 
 */
$entityTypeId = Mage::getModel('eav/entity')
                ->setType('catalog_product')
                ->getTypeId();

// workspace preference
$setup->addAttribute($entityTypeId, 'web2print_workspace_preference', array(
    'group'         				=> 'Chili Web2print',
    'input'         				=> 'text',
    'type'          				=> 'text',
    'label'        				=> 'Workspace preference',
    'backend'       				=> '',
    'visible'       				=> 1,
    'required'      				=> 0,
    'user_defined'                              => 1,
    'searchable' 				=> 0,
    'filterable' 				=> 0,
    'comparable'    				=> 0,
    'visible_on_front'	 			=> 0,
    'visible_in_advanced_search'                => 0,
    'is_html_allowed_on_front'                  => 0,
    'global'        				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'unique'            			=> false,
    'apply_to'          			=> 'simple,configurable,virtual,bundle,downloadable',
    'is_configurable'   			=> false,
    'frontend'                                  => 'web2print/entity_attribute_frontend_resourcebrowser',
    'note'                                      => 'Workspaces'
));


// view preference
$setup->addAttribute($entityTypeId, 'web2print_view_preference', array(
    'group'         				=> 'Chili Web2print',
    'input'         				=> 'text',
    'type'          				=> 'text',
    'label'        				=> 'View preference',
    'backend'       				=> '',
    'visible'       				=> 1,
    'required'      				=> 0,
    'user_defined' 				=> 1,
    'searchable' 				=> 0,
    'filterable' 				=> 0,
    'comparable'    				=> 0,
    'visible_on_front'	 			=> 0,
    'visible_in_advanced_search'                => 0,
    'is_html_allowed_on_front'                  => 0,
    'global'        				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'unique'            			=> false,
    'apply_to'          			=> 'simple,configurable,virtual,bundle,downloadable',
    'is_configurable'   			=> false,
    'frontend'                                  => 'web2print/entity_attribute_frontend_resourcebrowser',
    'note'                                      => 'ViewPreferences'
));


// document constraints
$setup->addAttribute($entityTypeId, 'web2print_document_constraint', array(
    'group'         				=> 'Chili Web2print',
    'input'         				=> 'text',
    'type'          				=> 'text',
    'label'        				=> 'Document constraints',
    'backend'       				=> '',
    'visible'       				=> 1,
    'required'      				=> 0,
    'user_defined' 				=> 1,
    'searchable' 				=> 0,
    'filterable' 				=> 0,
    'comparable'    				=> 0,
    'visible_on_front'	 			=> 0,
    'visible_in_advanced_search'                => 0,
    'is_html_allowed_on_front'                  => 0,
    'global'        				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'unique'            			=> false,
    'apply_to'          			=> 'simple,configurable,virtual,bundle,downloadable',
    'is_configurable'   			=> false,
    'frontend'                                  => 'web2print/entity_attribute_frontend_resourcebrowser',
    'note'                                      => 'DocumentConstraints'
));


/**
 * Add the custom category attributes 
 */
$categoryTypeId = Mage::getModel('eav/entity')
                ->setType('catalog_category')
                ->getTypeId();

// add attribute group
$categoryTypeId     = $installer->getEntityTypeId('catalog_category');
$attributeSetId     = $installer->getDefaultAttributeSetId($categoryTypeId);

$setup->addAttributeGroup($categoryTypeId, $attributeSetId, 'Chili Web2Print', 5);

// workspace preference
$setup->addAttribute($categoryTypeId, 'web2print_workspace_preference', array(
    'group'         				=> 'Chili Web2print',
    'input'         				=> 'text',
    'type'          				=> 'text',
    'label'        					=> 'Workspace preference',
    'backend'       				=> '',
    'visible'       				=> 1,
    'required'      				=> 0,
    'user_defined'                  => 1,
    'searchable' 					=> 0,
    'filterable' 					=> 0,
    'comparable'    				=> 0,
    'visible_on_front'	 			=> 0,
    'visible_in_advanced_search'    => 0,
    'is_html_allowed_on_front'      => 0,
    'global'        				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'frontend'						=> 'web2print/entity_attribute_frontend_resourcebrowser',
    'note'                          => 'Workspaces'
));
$setup->addAttribute($categoryTypeId, 'web2print_view_preference', array(
    'group'         				=> 'Chili Web2print',
    'input'         				=> 'text',
    'type'          				=> 'text',
    'label'        					=> 'View preference',
    'backend'       				=> '',
    'visible'       				=> 1,
    'required'      				=> 0,
    'user_defined'                  => 1,
    'searchable' 					=> 0,
    'filterable' 					=> 0,
    'comparable'    				=> 0,
    'visible_on_front'	 			=> 0,
    'visible_in_advanced_search'    => 0,
    'is_html_allowed_on_front'      => 0,
    'global'        				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'frontend'						=> 'web2print/entity_attribute_frontend_resourcebrowser',
    'note'                          => 'ViewPreferences'
));
$setup->addAttribute($categoryTypeId, 'web2print_document_constraint', array(
    'group'         				=> 'Chili Web2print',
    'input'         				=> 'text',
    'type'          				=> 'text',
    'label'        					=> 'Document constraints',
    'backend'       				=> '',
    'visible'       				=> 1,
    'required'      				=> 0,
    'user_defined'                  => 1,
    'searchable' 					=> 0,
    'filterable' 					=> 0,
    'comparable'    				=> 0,
    'visible_on_front'	 			=> 0,
    'visible_in_advanced_search'    => 0,
    'is_html_allowed_on_front'      => 0,
    'global'        				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'frontend'						=> 'web2print/entity_attribute_frontend_resourcebrowser',
    'note'                          => 'DocumentConstraints'
));


$installer->endSetup();
