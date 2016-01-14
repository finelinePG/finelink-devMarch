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
class Aitoc_Aitpermissions_Model_Advancedrole extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('aitpermissions/advancedrole');
    }

    public function getStoreviewIdsArray()
    {
        if (!$this->getStoreviewIds() || '0' == $this->getStoreviewIds())
        {
            return array();
        }
        return explode(',', $this->getStoreviewIds());
    }

    public function getCategoryIdsArray()
    {
        if (!$this->getCategoryIds() || '0' == $this->getCategoryIds())
        {
            return array();
        }
        return explode(',', $this->getCategoryIds());
    }

    public function canEditGlobalAttributes($roleId)
    {
        $recordCollection = $this->getCollection()->loadByRoleId($roleId);

        if ($recordCollection->getSize())
        {
            return (bool)$recordCollection->getFirstItem()->getCanEditGlobalAttr();
        }

        return true;
    }

    public function canEditOwnProductsOnly($roleId)
    {
        $recordCollection = $this->getCollection()->loadByRoleId($roleId);

        if ($recordCollection->getSize())
        {
            return (bool)$recordCollection->getFirstItem()->getCanEditOwnProductsOnly();
        }

        return true;
    }

    public function canCreateProducts($roleId)
    {
        $recordCollection = $this->getCollection()->loadByRoleId($roleId);

        if ($recordCollection->getSize())
        {
            return (bool)$recordCollection->getFirstItem()->getCanCreateProducts();
        }

        return true;
    }

    public function deleteRole($roleId)
    {
        $recordCollection = $this->getCollection()->loadByRoleId($roleId);

        if ($recordCollection->getSize())
        {
            foreach ($recordCollection as $record)
            {
                $record->delete();
            }
        }
    }
}