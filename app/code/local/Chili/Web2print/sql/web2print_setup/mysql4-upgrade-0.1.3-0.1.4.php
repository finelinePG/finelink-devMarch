<?php

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$entityTypeId = Mage::getModel('eav/entity')
                ->setType('catalog_product')
                ->getTypeId();

$setup->addAttribute($entityTypeId, 'web2print_frontend_pdf_profile', array(
    'group'                         => 'Chili Web2print',
    'input'                         => 'text',
    'type'                          => 'text',
    'label'                         => 'Preview PDF Export Settings',
    'backend'                       => '',
    'visible'                       => 1,
    'required'                      => 0,
    'user_defined'                  => 1,
    'searchable'                    => 0,
    'filterable'                    => 0,
    'comparable'                    => 0,
    'visible_on_front'              => 0,
    'visible_in_advanced_search'    => 0,
    'is_html_allowed_on_front'      => 0,
    'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'frontend'                      => 'web2print/entity_attribute_frontend_resourcebrowser',
    'note'                          => 'PdfExportSettings'
));


$setup->addAttribute($entityTypeId, 'web2print_backend_pdf_profile', array(
    'group'                         => 'Chili Web2print',
    'input'                         => 'text',
    'type'                          => 'text',
    'label'                         => 'Print-ready PDF Export Settings',
    'backend'                       => '',
    'visible'                       => 1,
    'required'                      => 0,
    'user_defined'                  => 1,
    'searchable'                    => 0,
    'filterable'                    => 0,
    'comparable'                    => 0,
    'visible_on_front'              => 0,
    'visible_in_advanced_search'    => 0,
    'is_html_allowed_on_front'      => 0,
    'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'frontend'                      => 'web2print/entity_attribute_frontend_resourcebrowser',
    'note'                          => 'PdfExportSettings'
));


$installer->endSetup();
