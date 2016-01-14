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
/**
 * This model used for rss feed
 */

class Aitoc_Aitpermissions_Model_Role_Front extends Aitoc_Aitpermissions_Model_Role
{
    protected function _getCurrentRoleId()
    {
        $session = Mage::getSingleton('admin/session');

        if ($user = $session->getUser())
        {
            return $user->getRole()->getId();
        }

        return 0;
    }
}