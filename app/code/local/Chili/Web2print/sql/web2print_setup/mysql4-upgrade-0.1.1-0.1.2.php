<?php

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
$entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();

/*this attribute will be shown under the Chili Web2print tab and defines if a html form must be visible or not when accessing the editor*/
$setup->addAttribute($entityTypeId, 'enable_editor_form', array(
    'group'         				=> 'Chili Web2print',
    'input'         				=> 'select',
    'type'          				=> 'int',
    'label'        				=> 'Enable html forms',
    'backend'       				=> '',
    'visible'       				=> 1,
    'required'      				=> 0,
    'user_defined' 				=> 0,
    'searchable' 				=> 0,
    'filterable'                                => 0,
    'comparable'    				=> 0,
    'visible_on_front'	 			=> 0,
    'visible_in_advanced_search'    => 0,
    'is_html_allowed_on_front'      => 0,
    'global'        				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'unique'            			=> false,
    'apply_to'          			=> 'simple,configurable,virtual,bundle,downloadable',
    'is_configurable'   			=> false,
    'source'                        => 'eav/entity_attribute_source_boolean',
    'default'                       => 0
));

$installer->endSetup();
