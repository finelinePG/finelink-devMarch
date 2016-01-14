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
class Aitoc_Aitunits_Model_Observer_Entity_Marker_Model_Adjgiftregitem extends Aitoc_Aitunits_Model_Observer_Abstract 
{
    
    protected function _getAllowedRoutes()
    {
        return array(
            'adjgiftreg_event_addItem',
            'aiteditablecart_cart_updatePost'
        );
    }
    
    public function mark($observer)
    {
        if( !in_array( $this->_getRoute() , $this->_getAllowedRoutes()) )
        {
            return ;
        }
        $this->_initEvent($observer);
        $obj = $this->_getEvent()->getObject();
        if(!($obj instanceof AdjustWare_Giftreg_Model_Item))
        {
            return;
        }
        $mark = new Aitoc_Aitunits_Model_Entity_Mark;
        $mark->addHandler('Aitoc_Aitunits_Model_Observer_Product_Qty_Changer_Addtoregistry');
        $mark->insertInObject($obj);
    }
}