
/**
 * All-In-One Checkout v1.0.15 : All-In-One Checkout v1.0.15 (OPCB Unit)
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcheckout / Aitoc_Aitcheckout
 * @version      1.0.15 - 1.4.15
 * @license:     n/a
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
var AitShipping = Class.create(AitAddress,  
{
    initShipping: function(checkboxId)
    {   
        if ($(checkboxId)) 
        {
            $(checkboxId).observe('click', this.onChangeUseForShipping.bind(this, checkboxId));
        } 
        Event.observe(window, 'load', this.onChangeUseForShipping.bind(this, checkboxId));
    },
    
    onChangeUseForShipping: function(checkboxId, event)
    {  
        if (typeof this.billingCurrentReloadSteps == "undefined") {
            this.billingCurrentReloadSteps = this.getCheckout().getStep('billinglocation').reloadSteps;
        }
        if ($(checkboxId)) 
        {
            if ($(checkboxId).checked) 
            {
                Element.hide(this.container);
                this.getCheckout().getStep('billing').update(event);
                this.getCheckout().getStep('billinglocation').setReloadSteps(this.getCheckout().getStep('shippinglocation').reloadSteps);
                this.getCheckout().getStep('billinglocation').update(event);
                return;
            }            
        } 
        Element.show(this.container);
        Element.show(this.container + '-child');
        this.getCheckout().getStep('billing').update(event);
        this.getCheckout().getStep('shipping').update(event);
        this.getCheckout().getStep('billinglocation').setReloadSteps(this.billingCurrentReloadSteps);
        this.getCheckout().getStep('billinglocation').update(event);
        this.getCheckout().getStep('shippinglocation').update(event);
    },

    afterInit: function()
    {
        this.initAddress(this.ids.addressSelect, this.ids.addressForm, 'shipping');
        this.initShipping(this.ids.useForShipping);


        aitCheckout.createStep(this.name + 'location',this.urls, {
            doCheckErrors : this.doCheckErrors,
            isLoadWaiting : this.isLoadWaiting,
            isUpdateOnReload : this.isUpdateOnReload,
            container : this.container + 'location',
			parent : this
        });
    }
});