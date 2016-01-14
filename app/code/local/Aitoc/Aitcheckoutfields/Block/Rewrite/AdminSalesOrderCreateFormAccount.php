<?php
/**
 * All-In-One Checkout v1.0.15 : All-In-One Checkout v1.0.15 (CFM Unit)
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcheckout / Aitoc_Aitcheckoutfields
 * @version      1.0.15 - 2.9.15
 * @license:     jC7sr77MwqoHj2SDR8w4YXR3o3w7irXLNFUdRYpgyc
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitcheckoutfields_Block_Rewrite_AdminSalesOrderCreateFormAccount  extends Mage_Adminhtml_Block_Sales_Order_Create_Form_Account
{
    protected function _toHtml()
    {
    	$html = parent::_toHtml();
    	$fBlock = $this->getLayout()->createBlock('aitcheckoutfields/ordercreate_form')->toHtml();
    	return $html.$fBlock;
    }
}