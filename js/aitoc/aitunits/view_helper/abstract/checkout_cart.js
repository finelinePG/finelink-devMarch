
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
var aitunitsSelectQtyHelperAbstractCheckoutCart = Class.create
(aitunitsSelectQtyHelperAbstract,{
    
    renderDropdown: function()
    {
        var reqEl = this.getReqElement().first();
        var attr = reqEl.attributes;
        this.requiredValue = attr['value'].value;
        var html = this.getDropdownHtml();
        Element.replace(reqEl,html);
        this.initDropdown(attr);
    },
    
    initDropdown:function(oldAttr)
    {
        var reqEl = $('aitunits_dropdown_'+this.id);
        reqEl.name = oldAttr['name'].value;
        reqEl.title = oldAttr['title'].value;
        reqEl.addClassName(oldAttr['class'].value);
        reqEl.addClassName('aitunits-selector-dropdown');
    },
    
    getReqElement: function()
    {
        var selectorExpr = 'input[name="cart['+ this.itemId +'][qty]"]';
        return $('shopping-cart-table').select(selectorExpr);
    }
    
});