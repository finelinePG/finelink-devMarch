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
class Aitoc_Aitunits_Block_Category_Product_Quantity_Selector_Type_Dropdown 
    extends Aitoc_Aitunits_Block_Category_Product_Quantity_Selector_Type_Abstract
{
    
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('aitunits/product/quantity/selector/type/dropdown.phtml');
    }
    
}