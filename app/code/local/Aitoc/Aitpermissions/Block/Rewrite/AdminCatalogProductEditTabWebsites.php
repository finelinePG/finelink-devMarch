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
class Aitoc_Aitpermissions_Block_Rewrite_AdminCatalogProductEditTabWebsites
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Websites
{
    public function getWebsiteCollection()
    {
    	$collection = Mage::getModel('core/website')->getResourceCollection();

        $role = Mage::getSingleton('aitpermissions/role');

    	if ($role->isPermissionsEnabled())
        {
            $collection->addIdFilter($role->getAllowedWebsiteIds());
        }
        
        return $collection->load();
    }
}