
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

var aitunitsForm = Class.create
({
    initialize: function(params)
    {
        this.id = 'aitunits_product_addtocart_form';
    },
    
    send: function(url,method,id)
    {
        //prepare form
        var form = $('aitunits_product_addtocart_form');
        form.action = url;
        if($('aitunits_qty_'+id))
        {
            min_val = eval('Math.min.apply(null, aitunitsSelectBlock_'+id+'.aAllowedValues)');
            form.qty.value = parseInt($('aitunits_qty_'+id).value) ? $('aitunits_qty_'+id).value : min_val;
        }
        else
        {
            form.qty.value = '1';
        }
        //send form
        if(method == 'cart')
        {
            productAddToCartForm.submit(this);
            return;
        }
        if(method == 'wishlist'||method == 'giftregistry')
        {
            productAddToCartForm.submitLight(this, url); 
            return false;
        }
    },
    
    setProductQty: function(productId,qty)
    {
        var elName = this.getQtyProductElName(productId);
        if(!this.hasInputElement(elName))
        {
            this.addQtyProductEl(productId);
        }
        var inputEl = this.getForm().select('input[name="'+elName+'"]').first();
        inputEl.value = qty;
    },
    
    hasInputElement: function(elName)
    {
        var numEl = this.getForm().select('input[name="'+elName+'"]').length;
        if(numEl > 0)
        {
            return numEl;
        }
        return false;
    },
    
    addQtyProductEl: function(productId)
    {
        var inputEl = document.createElement('input');
        Element.extend(inputEl);
        inputEl.type = 'hidden';
        inputEl.setAttribute('name', this.getQtyProductElName(productId));
        this.getForm().select('.no-display').first().insert({
            bottom: inputEl
        });
    },
    
    getQtyProductElName: function(itemId)
    {
        return this.id+'[item]'+'['+itemId+']'+'[qty]';
    },
    
    getForm: function()
    {
        return $(this.id);
    }
});

//var productAddToCartForm = new VarienForm('aitunits_product_addtocart_form');
//productAddToCartForm.submit = function(button, url) {
//    if (this.validator.validate()) {
//        var form = this.form;
//        var oldUrl = form.action;
//
//        if (url) {
//            form.action = url;
//        }
//        var e = null;
//        try {
//            this.form.submit();
//        } catch (e) {
//        }
//        this.form.action = oldUrl;
//        if (e) {
//            throw e;
//        }
//
//        if (button && button != 'undefined') {
//            button.disabled = true;
//        }
//    }
//}.bind(productAddToCartForm);
//
//productAddToCartForm.submitLight = function(button, url){
//    if(this.validator) {
//        var nv = Validation.methods;
//        delete Validation.methods['required-entry'];
//        delete Validation.methods['validate-one-required'];
//        delete Validation.methods['validate-one-required-by-name'];
//        if (this.validator.validate()) {
//            if (url) {
//                this.form.action = url;
//            }
//            this.form.submit();
//        }
//        Object.extend(Validation.methods, nv);
//    }
//}.bind(productAddToCartForm);