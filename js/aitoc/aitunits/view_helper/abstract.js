
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
var aitunitsSelectQtyHelperAbstract = Class.create
({
    initialize: function(params)
    {
        this.selectType = params.selectType;
        this.aAllowedValues = params.allowedValues;
        this.useOnlyAllowed = params.useOnlyAllowed;
        this.useMoreAllowed = params.useMoreAllowed;
        this.id = params.id;
        this.requiredValue = null;
        this.selectorHtml = params.selectorHtml;
        this.itemId = params.itemId;
    },

    render: function()
    {
        var selectType = this.selectType.charAt(0).toUpperCase()+this.selectType.substr(1);
        var expr = 'this.render'+ selectType + '();';
        eval(expr);
    },
    
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
        reqEl.id = oldAttr['id'].value;
    },
    
    
    renderArrows: function()
    {
        this.renderPlus();
    },
    
    renderPlus: function()
    {
        var reqEl = this.getReqElement().first();
        var attr = reqEl.attributes;
        this.requiredValue = attr['value'].value;
        Element.replace(reqEl,this.selectorHtml);
        var inputEl = $('aitunits_input_'+this.id);
        Element.replace(inputEl,reqEl);
        this.initPlus(reqEl);
    },
    
    initPlus: function(inputEl)
    {
        this.setReadOnly(inputEl);
        new aitunitsButtonControl(inputEl,$('aitunits_button_minus_'+this.id),$('aitunits_button_plus_'+this.id),
        {
            values: this.aAllowedValues
        });
    },
    
    
    renderSlider: function()
    {
        var reqEl = this.getReqElement().first();
        var oldElHtml = Element.replace(reqEl,this.getSliderHtml());
        Element.replace($('aitunits_input_'+this.id),oldElHtml);
        var inputEl = this.getReqElement().first();
        this.initSlider(inputEl);
    },
    
    initSlider: function(inputEl)
    {
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
                inputEl.setValue(value);
            },
            onChange: function(value) 
            {
                inputEl.setValue(value);
                cart.updatePost();
            }
        });
    },
    
    
    setReadOnly:function(inputEl)
    {
        if(this.useOnlyAllowed =='1')
        {
            inputEl.readOnly = true;
        }
    },
    
    changeToInput: function()
    {
        
    },
    
    changeToSelect: function()
    {
        
    },
    
    getDropdownHtml: function()
    {
        var required = this.getDropdownRequiredValue();
        var options = this.aAllowedValues.without(required);
        var html = '<select id="aitunits_dropdown_'+this.id+ '">';
        html +=    '<option selected="selected" value="'+ required +'">'+ required +'</option>';
        options.each(function(sOptionValue)
        {
            html +='<option value="'+ sOptionValue +'">'+ sOptionValue +'</option>';
        });
        html +=    '</select>';
        return html;
    },
    
    getSliderHtml: function()
    {
        return this.selectorHtml;
    },
    
    getInputHtml: function()
    {
        return '<input '+this.getInputOptions()+'>';
    },
    
    getDropdownRequiredValue: function()
    {
        if(this.requiredValue)
        {
            return this.requiredValue;
        }
        return this.aAllowedValues.first();
    }
    
});

var aitunitsAvailabilityHelperAbstract = Class.create
({
    initialize: function(indicatorEl,minusBtnEl,plusBtnEl,options)
    {
       this.indicatorEl = indicatorEl;
       this.minusBtnEl = minusBtnEl;
       this.plusBtnEl = plusBtnEl;
       
       this.values = options.values;
       this.value = options.value;
    },
    
    render: function()
    {
        
    }

});

var aitunitsUnitHelperAbstract = Class.create
({
    initialize: function(params)
    {
        
    },
    
    render: function()
    {
        
    }
    
});

var aitunitsButtonControl = Class.create
({
    initialize: function(indicatorEl,minusBtnEl,plusBtnEl,options)
    {
       this.indicatorEl = indicatorEl;
       this.minusBtnEl = minusBtnEl;
       this.plusBtnEl = plusBtnEl;
       
       this.values = options.values;
       this.value = options.value;
       
       this.onClick = options.onClick;
       
       this.min = 0;//Math.min.apply(null, this.values);
       this.max = Math.max.apply(null, this.values);
       
       Event.observe(plusBtnEl, 'mousedown', this.startIncr.bind(this));
       
       Event.observe(minusBtnEl, 'mousedown', this.startDiscr.bind(this));
       this.initialized = true;
    },
    
    startIncr: function()
    {
        this.values.sort(function(a,b){return a - b});
        var oldValue = parseInt(this.indicatorEl.value);
        var newValue = 0;
        if(oldValue >= this.max || !this.values )
        {
            newValue = oldValue; // + 1;
        }
        else
        {
            $A(this.values).each(function(item)
            {
                if(item > oldValue && newValue == 0)
                {
                    newValue = item;
                }
            });
        }
        this.indicatorEl.setValue(newValue);
        if (this.initialized && this.onClick)
        {
            this.onClick(newValue);
        }
        cart.updatePost();
    },
    
    startDiscr: function()
    {
        this.values.sort(function(a,b){return b - a});
        var oldValue = parseInt(this.indicatorEl.value);
        var newValue = 0;
        if(oldValue > this.max || !this.values)// || oldValue < this.min)
        {
            newValue = this.max;//oldValue- 1;
        }
        else
        {
            $A(this.values).each(function(item)
            {
                if(item < oldValue && newValue == 0)
                {
                    newValue = item;
                }
            });
        }
        if(newValue < 0)
        {
            newValue = 0;
        }
        this.indicatorEl.setValue(newValue);
        if (this.initialized && this.onClick)
        {
            this.onClick(newValue);
        }
        cart.updatePost();
    }
});

function aitunitsHasClassInElement(el,className)
{
    var delimiter = ' ';
    var aClassNames = el.className.toString().split (delimiter);
    var result = null;
    $A(aClassNames).each(function(name)
    {
        if(name==className)
        {
            result = true;
        }
    });
    return result;
};

function aitunitsAddClassNameinElement(el,className)
{
    var oldName = el.className.toString(); 
    el.className = oldName +' '+ className.toString();
};

//fix slider for ie9 ( magento 1.5 )

Control.Slider.prototype._isButtonForDOMEvents = function (event, code) {
    return event.which ? (event.which === code + 1) : (event.button === code);
};

Control.Slider.prototype.startDrag = function(event) {
if ((this._isButtonForDOMEvents(event,0))||Event.isLeftClick(event)) {
      if (!this.disabled){
        this.active = true;

        var handle = Event.element(event);
        var pointer  = [Event.pointerX(event), Event.pointerY(event)];
        var track = handle;
        if (track==this.track) {
          var offsets  = Position.cumulativeOffset(this.track);
          this.event = event;
          this.setValue(this.translateToValue(
           (this.isVertical() ? pointer[1]-offsets[1] : pointer[0]-offsets[0])-(this.handleLength/2)
          ));
          var offsets  = Position.cumulativeOffset(this.activeHandle);
          this.offsetX = (pointer[0] - offsets[0]);
          this.offsetY = (pointer[1] - offsets[1]);
        } else {
          // find the handle (prevents issues with Safari)
          while((this.handles.indexOf(handle) == -1) && handle.parentNode)
            handle = handle.parentNode;

          if (this.handles.indexOf(handle)!=-1) {
            this.activeHandle    = handle;
            this.activeHandleIdx = this.handles.indexOf(this.activeHandle);
            this.updateStyles();

            var offsets  = Position.cumulativeOffset(this.activeHandle);
            this.offsetX = (pointer[0] - offsets[0]);
            this.offsetY = (pointer[1] - offsets[1]);
          }
        }
      }
      Event.stop(event);
    }
  }; 
  
function aitunitsGetIeVersion()
{
    var version = 999; // we assume a sane browser
    if (navigator.appVersion.indexOf("MSIE") != -1)
    // bah, IE again, lets downgrade version number
    version = parseFloat(navigator.appVersion.split("MSIE")[1]);
    return version;
}