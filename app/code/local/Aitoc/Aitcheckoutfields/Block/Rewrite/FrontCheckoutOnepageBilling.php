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
class Aitoc_Aitcheckoutfields_Block_Rewrite_FrontCheckoutOnepageBilling extends Mage_Checkout_Block_Onepage_Billing
{
    protected $_mainModel;
    
    protected function _construct()
    {
        parent::_construct();
        $this->_mainModel = Mage::getModel('aitcheckoutfields/aitcheckoutfields');
    }
    
    public function getFieldHtml($aField)
    {
        $sSetName = 'billing';
        
        return $this->_mainModel->getAttributeHtml($aField, $sSetName, 'onepage');
    }
    
    public function getCustomFieldList($iTplPlaceId)
    {
        $iStepId = Mage::helper('aitcheckoutfields')->getStepId('billing');
        
        if (!$iStepId) return false;

        return $this->_mainModel->getCheckoutAttributeList($iStepId, $iTplPlaceId, 'onepage');
    }
    
    public function getRegCustomFieldList()
    {
        $iStepId = Mage::helper('aitcheckoutfields')->getStepId('billing');
        
        if (!$iStepId) return false;
        
        $fields = false;
        $fieldsTmp = $this->_mainModel->getCustomerAttributeList();
        
        if($fieldsTmp)
        {
            $fields = array();
            foreach($fieldsTmp as $placeholder)
            {
                foreach ($placeholder as $id => $data)
                {
                    if(!$data['is_searchable'])
                    {
                        $fields[$id]=$data;
                    }
                }
            }
        }
        return $fields;
    }
    
    public function checkStepHasRequired()
    {
        $iStepId = Mage::helper('aitcheckoutfields')->getStepId('shippinfo');
        
        if (!$iStepId) return false;

        return $this->_mainModel->checkStepHasRequired($iStepId, 'onepage');
    } 
}