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
class Aitoc_Aitunits_Model_Observer_Entity_Marker 
{
    
    public function catalogBlockProductListCollection(Varien_Event_Observer $observer)
    {
        $collection = $observer->getCollection();
        foreach($collection as $product)
        {
            $mark = new Aitoc_Aitunits_Model_Entity_Mark;
            $mark->addHandler('Aitoc_Aitunits_Model_Observer_Block_Replacer_Catalogproductprice');
            $mark->insertInObject($product);
        }
    }
}