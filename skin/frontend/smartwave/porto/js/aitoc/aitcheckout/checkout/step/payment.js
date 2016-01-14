
/**
 * All-In-One Checkout v1.0.15 : All-In-One Checkout v1.0.15 (OPCB Unit)
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcheckout / Aitoc_Aitcheckout
 * @version      1.0.15 - 1.4.15
 * @license:     n/a
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
var AitPayment = Class.create(Step,  
{
    initPayment: function(paymentContainerId)
    {
        this.initEvents(paymentContainerId);
        $(paymentContainerId).select('input[type="radio"]').each(function(input){
            input.addClassName('validate-one-required-by-name');
        });                
    },

    afterInit: function()
    {
        if(this.ids)
            this.initPayment(this.ids.paymentMethodLoad);
        this.setReloadSteps(['review']);
    }
});