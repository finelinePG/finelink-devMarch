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
class Aitoc_Aitpermissions_Model_Rewrite_AdminhtmlConfigData extends Mage_Adminhtml_Model_Config_Data
{

    public function load()
    {
        if ($this->getSection() != Mage::app()->getRequest()->getParam('section')) {
            $this->setSection(Mage::app()->getRequest()->getParam('section'));
            $this->_configData = null;
        }
        return parent::load();
    }
}