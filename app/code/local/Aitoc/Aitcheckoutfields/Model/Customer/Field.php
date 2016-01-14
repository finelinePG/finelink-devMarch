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
class Aitoc_Aitcheckoutfields_Model_Customer_Field extends Aitoc_Aitcheckoutfields_Model_Field_Abstract
{
    protected $_eventPrefix = 'aitcfm_customer_field';
    
    protected $_fieldType = 'customer';

    protected function _construct()
    {
        $this->_init('aitcheckoutfields/customer_field');
    }
}