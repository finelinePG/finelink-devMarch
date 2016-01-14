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
class Aitoc_Aitpermissions_Model_Permissions_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * @var int
     */
    protected $_roleId = 0;

    /**
     * @var Aitoc_Aitpermissions_Model_Advancedrole
     */
    protected $_role = null;

    protected function _construct()
    {
        if (Mage::app()->getStore()->isAdmin())
        {
            $session = Mage::getSingleton('admin/session');

            if ($user = $session->getUser())
            {
                $this->_getRole($user->getRole()->getId());
            }
        }
        return parent::_construct();
    }

    /**
     * Get advansed role by id
     *
     * @param null $roleId
     *
     * @return bool|Mage_Core_Model_Abstract|null
     */
    protected function _getRole($roleId = null)
    {
        if(!empty($roleId))
        {
            $this->_roleId = $roleId;
        }

        if(empty($this->_roleId))
        {
            return false;
        }

        if(empty($this->_role) || $this->_role->getRoleId() != $this->_roleId)
        {
            $this->_role = Mage::getModel('aitpermissions/advancedrole')->load($this->_roleId, 'role_id');
        }
        return $this->_role;
    }

    /**
     * Get permission (column from advanced role table)
     *
     * @param $permission
     * @param null $roleId
     *
     * @return bool
     */
    public function getPermission($permission, $roleId = null)
    {
        if($role = $this->_getRole($roleId))
        {
            return $role->getData($permission);
        }
        return false;

    }
}