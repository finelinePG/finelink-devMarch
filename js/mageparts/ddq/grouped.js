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
MageParts.Ddq.Grouped = Class.create(MageParts.Ddq.Product, {
    initialize: function($super, obj) {
        $super(obj);
    }
});
