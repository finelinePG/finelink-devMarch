
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
        var selectorExpr = 'input[id="qty"]';
        return $('product_addtocart_form').select(selectorExpr);
    }
    
});

var aitunitsAvailabilityHelper = Class.create
(aitunitsAvailabilityHelperAbstract,{
    initialize: function(params)
    {
        this.value = params.value; 
    },
    
    render: function()
    {
        var reqEl = $('product_addtocart_form').select('[class="availability out-of-stock"]').first();
        if(!reqEl)
        {
            reqEl = $('product_addtocart_form').select('[class="availability in-stock"]').first();
        }
        reqEl.down('span').update(this.value);
    }

});

var aitunitsUnitHelper = Class.create
(aitunitsUnitHelperAbstract,{
    initialize: function(params)
    {
        this.value = params.value;
        this.itemId = params.itemId;
    },
    
    render: function()
    {
        var block ='<span class="price" >'+ this.value +'</span>';
        var reqEl = $('product_addtocart_form').select('.price-box #product-price-'+this.itemId).first();
        reqEl.insert({bottom: block});
    }
    
});