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
class Aitoc_Aitunits_Model_Observer_Form_Data extends Aitoc_Aitunits_Model_Observer_Abstract 
{
    
    protected function _getAllowedRoutes()
    {
        return array(
            'adjgiftreg_event_addItem',
        );
    }
    
    public function toRegistry($observer)
    {
        if( !in_array( $this->_getRoute() , $this->_getAllowedRoutes()) )
        {
            return ;
        }
        $this->_initEvent($observer);
        $formName = Mage::getBlockSingleton('aitunits/category_product_form')->getId();
        $requestParams = Mage::helper('aitunits')->getFormRequestParams($formName);
        if(!$requestParams)
        {
            return;
        }
        Mage::register($formName, $requestParams);
        unset($_POST[$formName]);
        //$params = Mage::app()->getRequest()->getParams();
        //return;
    }
}