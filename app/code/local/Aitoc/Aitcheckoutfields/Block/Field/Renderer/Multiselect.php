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
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Date
 *
 * @author kirichenko
 */
class Aitoc_Aitcheckoutfields_Block_Field_Renderer_Multiselect extends Aitoc_Aitcheckoutfields_Block_Field_Renderer_Abstract 
{
    public function render() 
    {
        $values = explode(',', $this->sFieldValue[0]);

        $select = Mage::getModel('core/layout')->createBlock('core/html_select')
                    ->setName($this->sFieldName . '[]')
                    ->setId($this->sFieldId)
                    ->setTitle($this->sLabel)
                    ->setClass($this->sFieldClass)
                    ->setValue($values)
                    ->setExtraParams('multiple')
                    ->setOptions($this->aOptionHash);
                
                    $sHidden = '<input type="hidden" name="'.$this->sFieldName.'"  value="" />';
                    
                    return $sHidden . $select->getHtml();
    }
}

?>