<?php
/**
 * Product Units and Quantities
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitunits
 * @version      1.0.11
 * @license:     0JdTQfDMswel7fbpH42tkXIHe3fixI4GH3daX0aDVf
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2012 AITOC, Inc. 
 */
class Aitoc_Aitunits_Model_Product_Attribute_Source_Stock_Product_Qty extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Retrieve all attribute options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) 
        {
            $this->_options = Mage::getModel('aitunits/system_config_source_stock_product_qty')->toOptionArray(); 
        }
        return $this->_options;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColums()
    {
    	$attributeCode = $this->getAttribute()->getAttributeCode();
    	
    	if(version_compare(Mage::getVersion(),'1.6.0','<'))
        {
        	$column['type']     = 'VARCHAR(255)';
            $column['is_null']  = true;
            $column['default']  = null;
            $column['unsigned']  = false;
            $column['extra']  = null;
            
            return array($attributeCode => $column);
        }
        else 
        {
	        $column = array(
	            'unsigned'  => false,
	            'default'   => null,
	            'extra'     => null
	        );
	
	        $column['type']     = 'VARCHAR';
	        $column['length']   = 255;
	        $column['nullable'] = true;
	        $column['comment']  = $attributeCode . ' column';
        }
        
        return array($attributeCode => $column);
    }

    /**
     * Retrieve Indexes(s) for Flat
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        $indexes = array();

        $index = 'IDX_' . strtoupper($this->getAttribute()->getAttributeCode());
        $indexes[$index] = array(
            'type'      => 'index',
            'fields'    => array($this->getAttribute()->getAttributeCode())
        );

        return $indexes;
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}