<?php
/**
 * MageParts
 * 
 * NOTICE OF LICENSE
 * 
 * This code is copyrighted by MageParts and may not be reproduced
 * and/or redistributed without a written permission by the copyright 
 * owners. If you wish to modify and/or redistribute this file please
 * contact us at info@mageparts.com for confirmation before doing
 * so. Please note that you are free to modify this file for personal
 * use only.
 *
 * If you wish to make modifications to this file we advice you to use
 * the "local" file scope in order to aviod conflicts with future updates. 
 * For information regarding modifications see http://www.magentocommerce.com.
 *  
 * DISCLAIMER
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE 
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES 
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF 
 * USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY 
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE 
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * @category   MageParts
 * @package    MageParts_Ddq
 * @copyright  Copyright (c) 2009 MageParts (http://www.mageparts.com/)
 * @author     MageParts Crew
 */

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');

$installer->startSetup();

$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'ddq_qty_list');
$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'ddq_preselected');
$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'ddq_enabled');
$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'ddq_incremental');
$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'ddq_hide_unavailable_qty');
$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'ddq_layout');

if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'ddq_qty_list')) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'ddq_qty_list', array(
        'label'                   => 'Quantity Options',
        'type'                    => 'text',
        'input'                   => 'text',
        'backend'                 => 'eav/entity_attribute_backend_serialized',
        'input_renderer'          => 'ddq/adminhtml_catalog_product_form_renderer_quantityoption',
        'group'                   => 'General',
        'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'                 => true,
        'required'                => false,
        'user_defined'            => true,
        'visible_on_front'        => false,
        'used_in_product_listing' => true
    ));
}

if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'ddq_preselected')) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'ddq_preselected', array(
        'label'                   => 'Preselected Option',
        'input'                   => 'text',
        'type'                    => 'text',
        'group'                   => 'General',
        'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'                 => true,
        'required'                => false,
        'user_defined'            => true,
        'visible_on_front'        => false,
        'used_in_product_listing' => true
    ));
}

if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'ddq_enabled')) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'ddq_enabled', array(
        'label'                   => 'Enabled',
        'input'                   => 'select',
        'source'                  => 'eav/entity_attribute_source_boolean',
        'group'                   => 'General',
        'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'                 => true,
        'required'                => false,
        'user_defined'            => true,
        'default_value'           => 1,
        'visible_on_front'        => false,
        'used_in_product_listing' => true
    ));
}

if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'ddq_incremental')) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'ddq_incremental', array(
        'label'                   => 'Enable Incremental Quantities',
        'input'                   => 'select',
        'source'                  => 'eav/entity_attribute_source_boolean',
        'group'                   => 'General',
        'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'                 => true,
        'required'                => false,
        'user_defined'            => true,
        'default_value'           => 0,
        'visible_on_front'        => false,
        'used_in_product_listing' => true
    ));
}

if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'ddq_hide_unavailable_qty')) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'ddq_hide_unavailable_qty', array(
        'label'                   => 'Hide Unavailable Quantities',
        'input'                   => 'select',
        'source'                  => 'eav/entity_attribute_source_boolean',
        'group'                   => 'General',
        'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'                 => true,
        'required'                => false,
        'user_defined'            => true,
        'default_value'           => 0,
        'visible_on_front'        => false,
        'used_in_product_listing' => true
    ));
}

if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'ddq_layout')) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'ddq_layout', array(
        'label'                   => 'Layout',
        'input'                   => 'select',
        'source'                  => 'ddq/system_config_source_layout',
        'group'                   => 'General',
        'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'                 => true,
        'required'                => false,
        'user_defined'            => true,
        'default_value'           => 'select',
        'visible_on_front'        => false,
        'used_in_product_listing' => true
    ));
}

$installer->endSetup();
