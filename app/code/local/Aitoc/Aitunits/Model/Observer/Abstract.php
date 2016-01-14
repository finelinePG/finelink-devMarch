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
abstract class Aitoc_Aitunits_Model_Observer_Abstract 
{
    protected $_hasMarkFilter = false;
    protected $_event;
    
    protected function _init()
    {
        return;
    }

    protected function _getRoute()
    {
        $request = Mage::app()->getRequest();
        $route = $request->getRequestedRouteName().'_'.$request->getRequestedControllerName().'_'.$request->getRequestedActionName();
        return $route;
    }
    
    abstract protected function _getAllowedRoutes();
    
    
    protected function _addMarkFilter()
    {
        $this->_hasMarkFilter = true;
        return $this;
    }
    
    //only for product
    protected function _checkInProductMark()
    {
        if($this->_hasMarkFilter)
        {
            $product = $this->_block->getProduct();
            return $this->_hasMark($product);
        }
        return true;
    }
    
    protected function _hasMark(Varien_Object $obj)
    {
        if($obj->hasData('aitunits_mark'))
        {
            if($obj->getAitunitsMark()->hasHandler(get_class($this)))
            {
                return true;
            }
        }
        return false;
    }
    
    protected function _getEvent()
    {
        return $this->_event;
    }
    
    protected function _initEvent($observer)
    {
        $this->_event = $observer->getEvent();
    }
}