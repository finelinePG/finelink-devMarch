<?php
/**
 * All-In-One Checkout v1.0.15 : All-In-One Checkout v1.0.15 (OPCB Unit)
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcheckout / Aitoc_Aitcheckout
 * @version      1.0.15 - 1.4.15
 * @license:     jC7sr77MwqoHj2SDR8w4YXR3o3w7irXLNFUdRYpgyc
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitcheckout_Block_Customer_Widget_Name extends Mage_Customer_Block_Widget_Name
{
    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('aitcheckout/customer/widget/name.phtml');
    }
    
    private $_showAmount = null;
    public function suffixBlockAmount() {
        if($this->_showAmount == null) {
            $this->_showAmount = ($this->showPrefix()?1:0) + ($this->showSuffix()?1:0) + ($this->showMiddlename()?1:0);
        }
        return $this->_showAmount;
    }

}