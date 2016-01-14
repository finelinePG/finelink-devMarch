
/**
 * All-In-One Checkout v1.0.15 : All-In-One Checkout v1.0.15 (OPCB Unit)
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcheckout / Aitoc_Aitcheckout
 * @version      1.0.15 - 1.4.15
 * @license:     n/a
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
var AitGiftwrap = Class.create(Step,  
{
    initGiftwrap: function()
    {
        this.initEvents(this.container);
    },

    afterInit: function()
    {
        this.initGiftwrap();
        this.setReloadSteps(['payment', 'review']);

        if (this.isShowCartInCheckout)
        {
            this.addReloadSteps(['messages']);
        }

        if (aitCheckout.getStep('coupon'))
        {
            aitCheckout.getStep('coupon').addReloadSteps(['aitgiftwrap']);
        }

        if (aitCheckout.getStep('review') && this.isShowCartInCheckout)
        {
            aitCheckout.getStep('review').addReloadSteps(['aitgiftwrap']);
        }

        aitCheckout.setStep('aitgiftwrap', this);
    }
    
});