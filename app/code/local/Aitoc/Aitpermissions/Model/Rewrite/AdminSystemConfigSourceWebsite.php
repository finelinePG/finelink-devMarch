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
class Aitoc_Aitpermissions_Model_Rewrite_AdminSystemConfigSourceWebsite extends Mage_Adminhtml_Model_System_Config_Source_Website
{
    public function toOptionArray()
    {
        $this->_options = parent::toOptionArray();
        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            foreach ($this->_options as $id => $website)
            {
                if (!in_array($website['value'], $role->getAllowedWebsiteIds()))
                {
                    unset($this->_options[$id]);
                }
            }
        }
        return $this->_options;

    }
}