
/**
 * Product Units and Quantities
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitunits
 * @version      1.0.11
 * @license:     n/a
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2012 AITOC, Inc. 
 */
var aitunitsSelectQtyHelper = Class.create
(aitunitsSelectQtyHelperAbstract,{
    
    getReqElement: function()
    {
        var selectorExpr = 'input[name="qty['+ this.itemId +']"]';
        return $('giftreg-table').select(selectorExpr);
    }

});