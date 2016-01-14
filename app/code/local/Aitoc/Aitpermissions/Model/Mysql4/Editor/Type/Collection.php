<?php
/**
 * Advanced Permissions
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitpermissions
 * @version      2.10.9
 * @license:     bJ9U1uR7Gejdp32uEI9Z7xOqHZ5UnP25Ct3xHTMyeC
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitpermissions_Model_Mysql4_Editor_Type_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('aitpermissions/editor_type');
    }

    public function loadByRoleId($roleId)
    {
        $this->addFieldToFilter('role_id', $roleId);
        $this->load();
        return $this;
    }

    public function duplicateProductTypePermissions($oldRoleId, $newRoleId)
    {
        $oldTypes = $this->loadByRoleId($oldRoleId);
        
        foreach($oldTypes as $type)
        {
            $type->setData('id', null);
            $type->setData('role_id', $newRoleId);
            $type->setData('type',$type->getType());
            $type->save();
        }        
    }
}