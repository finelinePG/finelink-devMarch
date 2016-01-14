<?php
/**
 * Product Units and Quantities
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitunits
 * @version      1.0.11
 * @license:     0JdTQfDMswel7fbpH42tkXIHe3fixI4GH3daX0aDVf
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2012 AITOC, Inc. 
 */
class Aitoc_Aitunits_Model_Rewrite_Adjgiftregitem extends AdjustWare_Giftreg_Model_Item 
{
    public function _construct()
    {
        parent::_construct();
        Mage::dispatchEvent('aitunits_model_mark', array('object'=>$this));
    }
    
//    public function loadBy($eventId, $productId)
//    {
//        parent::loadBy($eventId, $productId);
//        Mage::dispatchEvent('aitunits_model_mark', array('object'=>$this));
//        return $this;
//    }
}