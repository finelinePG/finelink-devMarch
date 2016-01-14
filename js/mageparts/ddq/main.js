/**
 * Create MageParts Namespace.
 */
if (typeof MageParts === "undefined") {
    var MageParts = {};
}

/**
 * Quantity Options JavaScript object.
 *
 * This object is used on several pages, order view, edit product,
 * ddq system config section, product attributes update etc.
 */
MageParts.Ddq = Class.create({

    /**
     * Constructor.
     *
     * The keys in the config argument should correspond
     * with the variables described in this object, whether
     * private or shared. It should be noted that variable
     * names starting with an underscore will be ignored.
     *
     * @param obj config
     */
    initialize: function(config)
    {
        // apply submitted configuration
        config = config || {};

        for (var key in this) {
            if (key in config) {
                if (key.indexOf('_') !== 0) {
                    if (typeof config[key] !== "undefined") {
                        this[key] = config[key];
                    }
                }
            }
        }
    },

    /**
     * Check whether or not an object is empty.
     *
     * @param obj
     * @returns {boolean}
     */
    isEmpty: function(obj) {
        var result = true;

        for (var prop in obj) {
            if (obj.hasOwnProperty(prop)) {
                result = false;
                break;
            }
        }
        return result;
    },

    compareVersions: function(version1, version2, symbol) {
        var result;
        var num1 = parseInt(version1.split('.').join(''), 10);
        var num2 = parseInt(version2.split('.').join(''), 10);

        switch(symbol) {
            case '<'  : result = (num1 < num2);   break;
            case '>'  : result = (num1 > num2);   break;
            case '<=' : result = (num1 <= num2);  break;
            case '>=' : result = (num1 >= num2);  break;
            case '==' : result = (num1 === num2); break;
            default   : result = -1;
        }

        return result;
    },

    copyObject: function(obj) {
        var i, copy  = {};
        var toString = Object.prototype.toString;

        for (i in obj) {
            if (obj.hasOwnProperty(i)) {
                switch(toString.call(obj[i])) {
                    case '[object Array]'  : copy[i] = this.copyArray(obj[i]);  break;
                    case '[object Object]' : copy[i] = this.copyObject(obj[i]); break;
                    default                : copy[i] = obj[i];
                }
            }
        }

        return copy;
    },

    copyArray: function(array) {
        var copy     = [];
        var i, len   = array.length;
        var toString = Object.prototype.toString;

        for (i = 0; i < len; ++i) {
            switch(toString.call(array[i])) {
                case '[object Array]'  : copy[i] = this.copyArray(array[i]);  break;
                case '[object Object]' : copy[i] = this.copyObject(array[i]); break;
                default                : copy[i] = array[i];
            }
        }

        return copy;
    },

    getPriceElements: function(idNum) {
        var priceEl, unitPriceEl, oldPriceEl, exclEl, inclEl, unitInclEl, unitExclEl, cartOffsetEl;
        var taxType = this.getTaxTypes();

        if (MP_DDQ_SETTINGS.view === 'cart') {
            cartOffsetEl = $('mp-ddq-el-' + idNum + '-select').up('td', 0);

            if (taxType === 'both') {
                exclEl     = cartOffsetEl.next('td', 0).select('.price')[0];
                inclEl     = cartOffsetEl.next('td', 1).select('.price')[0];
                unitExclEl = cartOffsetEl.previous('td', 1).select('.price')[0];
                unitInclEl = cartOffsetEl.previous('td', 0).select('.price')[0];
            }
            else {
                if (taxType === 'incl') {
                    inclEl     = cartOffsetEl.next('td', 0).select('.price')[0];
                    unitInclEl = cartOffsetEl.previous('td', 0).select('.price')[0];
                }
                else if (taxType === 'excl') {
                    exclEl     = cartOffsetEl.next('td', 0).select('.price')[0];
                    unitExclEl = cartOffsetEl.previous('td', 0).select('.price')[0];
                }
                else {
                    priceEl     = cartOffsetEl.next('td', 0).select('.price')[0];
                    unitPriceEl = cartOffsetEl.previous('td', 0).select('.price')[0];
                }
            }
        }
        else {
            if (taxType === 'both') {
                exclEl = $('price-excluding-tax-' + idNum);
                inclEl = $('price-including-tax-' + idNum);
            }
            else {
                if (taxType === 'incl') {
                    inclEl = $('product-price-' + idNum);
                }
                else if (taxType === 'excl') {
                    exclEl = $('product-price-' + idNum);
                }
                else {
                    priceEl = $('product-price-' + idNum);
                }
            }

            oldPriceEl = $('old-price-' + idNum);
        }

        return {
            price:        priceEl,
            oldPrice:     oldPriceEl,
            unitPrice:    unitPriceEl,
            excl:         exclEl,
            incl:         inclEl,
            unitExcl:     unitExclEl,
            unitIncl:     unitInclEl
        };
    },

    hasOptions: function() {
        return (window.opConfig && !this.isEmpty(opConfig.config));
    },

    getTaxTypes: function() {
        var incl = MP_DDQ_SETTINGS.cartInclTax;
        var excl = MP_DDQ_SETTINGS.cartExclTax;
        var type = 'none';

        if (incl && excl) {
            type = 'both';
        }
        else if (incl) {
            type = 'incl';
        }
        else if (excl) {
            type = 'excl';
        }

        return type;
    },

    getOptionsPrices: function() {
        var prices, obj = null;

        if (optionsPrice && this.productType === 'bundle') {
            prices = optionsPrice.getOptionPrices();
            obj = {
                price: prices[0],
                nonTaxable: prices[1],
                oldPrice: prices[2],
                pricInclTax: prices[3]
            };
        }

        return obj;
    }

});
