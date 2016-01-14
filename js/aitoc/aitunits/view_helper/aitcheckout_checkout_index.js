
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
(aitunitsSelectQtyHelperAbstractCheckoutCart,{
    
    initPlus: function(inputEl)
    {
        this.setReadOnly(inputEl);
        new aitunitsButtonControl(inputEl,$('aitunits_button_minus_'+this.id),$('aitunits_button_plus_'+this.id),
        {
            values: this.aAllowedValues,
            onClick: function(value)
            {
                cart.aitunitsUpdatePost().bind(cart);
            }
        });
    },
    
    initSlider: function(inputEl)
    {
        this.setReadOnly(inputEl);
        var iMinAllowedValue = Math.min.apply(null, this.aAllowedValues);
        var iMaxAllowedValue = Math.max.apply(null, this.aAllowedValues);
        var slider = new Control.Slider( 'aitunits_slider_handle_'+this.id,'aitunits_slider_track_'+this.id,
        {
            axis:'horizontal',
            range: $R(iMinAllowedValue,iMaxAllowedValue),
            values: this.aAllowedValues,
            sliderValue: this.requiredValue,
            onSlide: function(value) 
            {
                inputEl.value = value;
            },
            onChange: function(value) 
            { 
                inputEl.value = value;
                cart.aitunitsUpdatePost().bind(cart);
            }
        });
    }

});