/**
 * Make sure the main JavaScript file for this extension has loaded.
 */
if (typeof MageParts === "undefined") {
    alert("Unable to load Quantity Options main JavaScript file.");
}

/**
 * Quantity Options JavaScript object.
 *
 * This object provides functions to setup option entries and other good stuff.
 */
MageParts.Ddq.Bundle = Class.create(MageParts.Ddq.Product, {
    initialize: function($super, obj) {
        $super(obj);
    },

    setPrice: function(idNum, bundleEl) {
        var bundleCopy = this.copyObject(bundle.config.options);
        var qtyPrices  = this.getQtyPrices(idNum);

        if (this.hasOptions()) {
            this.setCustomOptionPrices(idNum);
        }

        optionsPrice.productPrice    = qtyPrices.p;
        optionsPrice.productOldPrice = (qtyPrices.hasOwnProperty('o')) ? qtyPrices.o : qtyPrices.p;
        optionsPrice.priceInclTax    = (qtyPrices.hasOwnProperty('i')) ? qtyPrices.i : qtyPrices.p;
        optionsPrice.priceExclTax    = (qtyPrices.hasOwnProperty('e')) ? qtyPrices.e : qtyPrices.p;
        optionsPrice.reload();

        this.setPriceObjectPrices(bundle.config.options, this.getQty(idNum));

        if (bundleEl) {
            bundle.changeSelection(bundleEl);
        }
        else {
            bundle.reloadPrice();
        }

        bundle.config.options = bundleCopy;
    }
});
