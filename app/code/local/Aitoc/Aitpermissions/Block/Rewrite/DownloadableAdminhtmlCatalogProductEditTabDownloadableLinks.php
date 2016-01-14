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
class Aitoc_Aitpermissions_Block_Rewrite_DownloadableAdminhtmlCatalogProductEditTabDownloadableLinks
    extends Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links
{
    public function getPurchasedSeparatelySelect()
    {        
        $html = parent::getPurchasedSeparatelySelect();

        $role = Mage::getSingleton('aitpermissions/role');

        if (!Mage::app()->isSingleStoreMode() && 
            $role->isPermissionsEnabled() &&
            !$role->canEditGlobalAttributes())
        {
            $html = str_replace('<select', '<select disabled="disabled"', $html);         
        }

        return $html;
    }
}