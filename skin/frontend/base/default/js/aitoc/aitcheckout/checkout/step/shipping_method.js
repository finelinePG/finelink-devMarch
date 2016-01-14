
/**
 * All-In-One Checkout v1.0.15 : All-In-One Checkout v1.0.15 (OPCB Unit)
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcheckout / Aitoc_Aitcheckout
 * @version      1.0.15 - 1.4.15
 * @license:     n/a
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
var AitShippingMethod = Class.create(Step,  
    {
        initShippingMethod: function(shippingMethodContainerId)
        {
            this.initEvents(shippingMethodContainerId);
        },

        afterInit: function()
        {
            this.initShippingMethod(this.ids.loadContainer);

            if (aitCheckout.getStep('shipping'))
            {
                aitCheckout.getStep('shippinglocation').setReloadSteps(['shipping_method']);
            }
            else
            {
                aitCheckout.getStep('billinglocation').setReloadSteps(['shipping_method']);
                aitCheckout.getStep('billinglocation').initVirtualUpdate();
            }

            this.setReloadSteps(['payment', 'review']);
        }
    });