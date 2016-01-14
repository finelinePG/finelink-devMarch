
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
    
    renderDropdown: function()
    {
        var reqEl = this.getReqElement();
        reqEl.update(this.getDropdownHtml());
        this.initDropdown();
        $(this.getQtyInputElId()).observe('change',this.handleChangeProductQty.bind(this));
    },
    
    initDropdown:function()
    {
        var reqEl = $('aitunits_dropdown_'+this.id);
        reqEl.name = 'aitunits_qty_'+this.id;  
        reqEl.addClassName('input-text qty aitunits-selector-dropdown');
        reqEl.id = 'aitunits_qty_'+this.id;
    },
    
    renderArrows: function()
    {
        this.renderPlus();
    },
    
    renderPlus: function()
    {
        Element.replace($('aitunits_input_'+this.id),this.getInputHtml());
        var inputEl = $('aitunits_qty_'+this.id);
        this.initPlus(inputEl);
        inputEl.value = Math.min.apply(null, this.aAllowedValues);
        $(this.getQtyInputElId()).observe('change',this.handleChangeProductQty.bind(this));
        $('aitunits_button_minus_'+this.id).observe('click',this.handleChangeProductQty.bind(this));
        $('aitunits_button_plus_'+this.id).observe('click',this.handleChangeProductQty.bind(this));
    },
    
    renderSlider: function()
    {
        var reqEl = this.getReqElement();
        Element.replace($('aitunits_input_'+this.id),this.getInputHtml());
        var inputEl = $('aitunits_qty_'+this.id);
        this.initSlider(inputEl);
        inputEl.value = Math.min.apply(null, this.aAllowedValues);
        this.decorateSliderParentNode(reqEl.parentNode);
        $(this.getQtyInputElId()).observe('change',this.handleChangeProductQty.bind(this));
    },
    
    initSlider: function(inputEl)
    {
        var qtyHelper = this;
        this.setReadOnly(inputEl);
        var iMinAllowedValue = Math.min.apply(null, this.aAllowedValues);
        var iMaxAllowedValue = Math.max.apply(null, this.aAllowedValues);
        new Control.Slider( 'aitunits_slider_handle_'+this.id,'aitunits_slider_track_'+this.id,
        {
            axis:'horizontal',
            range: $R(iMinAllowedValue,iMaxAllowedValue),
            values: this.aAllowedValues,
            sliderValue: this.requiredValue,
            onSlide: function(value) 
            {
                inputEl.value = value;
                var qtyChangeHandler = qtyHelper.handleChangeProductQty.bind(qtyHelper);
                qtyChangeHandler();
            },
            onChange: function(value) 
            { 
                inputEl.value = value;
                var qtyChangeHandler = qtyHelper.handleChangeProductQty.bind(qtyHelper);
                qtyChangeHandler();
            }
        });
    },
    
    decorateSliderParentNode:function(reqEl)
    {
        if(aitunitsHasClassInElement(reqEl,'item'))
        {
            aitunitsAddClassNameinElement(reqEl,'aitunits-item-slider');
            return;
        }
        if(aitunitsHasClassInElement(reqEl,'f-fix'))
        {
            aitunitsAddClassNameinElement(reqEl,'aitunits-f-fix-slider');
        }
    },

    getInputOptions: function()
    {
        var name = 'aitunits_qty_'+this.id; 
        return 'id="'+name+'" name="'+name+'"'+ ' class="input-text qty aiunits-input" type="text" value="0" maxlength="12"';
    },
    
    getReqElement: function()
    {
        return $('aitunits_qty_container_'+this.id);
    },
    
    addQtyObserver: function()
    {
        $(this.getQtyInputElId()).observe('change',this.handleChangeProductQty.bind(this));
    },
    
    handleChangeProductQty: function(event)
    {
        var form = aitunitsForm_productQty;
        if(form !== undefined)
        {
            form.setProductQty(this.itemId, $(this.getQtyInputElId()).value);
        }
    },
    
    getQtyInputElId: function()
    {
        return 'aitunits_qty_'+this.id;
    }
    
});

// category grid decorate functions

function aitunitsCategoryGridDecorate()
{
    var gridItems = $$('div.category-products li.item');
    if(gridItems.lenght == 0)
    {
        return;
    }
    gridItems.each(function(item){
        if(!item.select('.aitunits-select-container').first())
        {
            return;
        }
        var linksContainer = item.select('div.actions ul.add-to-links').first();
        var childQty = linksContainer.children.length;
        if(childQty < 3)
        {
            return;
        }
        if(item.hasClassName('aitunits-item-slider'))
        {
            aitunitsGridItemSliderDecorate(item);
            return;
        }
        aitunitsGridItemDecorate(item);
    });
    return;
}

function aitunitsGridItemSliderDecorate(item)
{
    //var linksContainer = item.select('div.actions ul.add-to-links').first();
    var container = item.select('.aitunits-select-container').first();
    var ieVersion = aitunitsGetIeVersion();
   
    if (ieVersion < 8) 
    {
        container.setStyle({
            bottom: '95px'
        });
        item.setStyle({
            paddingBottom: '150px'
        });
        return;
    }
    container.setStyle({
        bottom: '85px'
    });
    item.setStyle({
        paddingBottom: '150px'
    });
}

function aitunitsGridItemDecorate(item)
{
    var container = item.select('.aitunits-select-container').first();
    container.setStyle({
        bottom: '95px'
    });
    item.setStyle({
        paddingBottom: '150px'
    });
}