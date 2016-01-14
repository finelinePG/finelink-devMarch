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
/* AITOC static rewrite inserts start */
/* $meta=%default,Aitoc_Aitloyalty% */
if(Mage::helper('core')->isModuleEnabled('Aitoc_Aitloyalty')){
    class Aitoc_Aitpermissions_Block_Rewrite_AdminReportSalesCouponsGrid_Aittmp extends Aitoc_Aitloyalty_Block_Rewrite_AdminhtmlReportSalesCouponsGrid {} 
 }else{
    /* default extends start */
    class Aitoc_Aitpermissions_Block_Rewrite_AdminReportSalesCouponsGrid_Aittmp extends Mage_Adminhtml_Block_Report_Sales_Coupons_Grid {}
    /* default extends end */
}

/* AITOC static rewrite inserts end */
class Aitoc_Aitpermissions_Block_Rewrite_AdminReportSalesCouponsGrid extends Aitoc_Aitpermissions_Block_Rewrite_AdminReportSalesCouponsGrid_Aittmp
{
    /*
    * @return Varien_Object
    */
    public function getFilterData()
    {
        $filter = parent::getFilterData();
        $filter->setStoreIds(
            implode(',', Mage::helper('aitpermissions/access')
                    ->getFilteredStoreIds(
                    $filter->getStoreIds() ? explode(',', $filter->getStoreIds()) : array()
                )
            )
        );
        return $filter;
    }
}