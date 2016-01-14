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
MageParts.Ddq.Product = Class.create(MageParts.Ddq, {
    initialize: function(config) {
        var key, that = this;

        config = config || {};

        this.enabled              = {};
        this.productIds           = [];
        this.qtyBoxPath           = '#qty';
        this.layouts              = {};
        this.qtyOptions           = {};
        this.priceFormat          = {};
        this.preselected          = {};
        //this.priceContainers      = [];
        this.elements             = {};
        //this.magentoVersion       = null;
        //this.hasProductOptions    = false;
        this.productType          = null;
        this.defaultId            = null;

        //this.qtyBoxAttributes     = {};

        for (key in this) {
            if (this.hasOwnProperty(key) && config.hasOwnProperty(key)) {
                if (key.indexOf('_') !== 0) {
                    this[key] = config[key];
                }
            }
        }

        document.observe('dom:loaded', function() {
            that.setEnabled();
            that.setElements();
            that.replaceInlineEvents();
            that.insertLayoutElements();

            for (key in that.elements) {
                if (that.elements.hasOwnProperty(key)) {
                    that.setObserver(key);
                }
            }

            if (that.productType === 'configurable' && MP_DDQ_SETTINGS.updateWithAjax) {
                that.setAjaxLoader();
            }
        });
    },

    insertLayoutElements: function() {
        var key, qtyBox, qtySpan;
        var updatePrices = MP_DDQ_SETTINGS.priceUpdatesEnabled;

        for (key in this.layouts) {
            if (this.layouts.hasOwnProperty(key)) {
                qtyBox  = this.getQtyBox(key);
                qtySpan = $('mp-ddq-el-' + key);

                if (qtyBox && qtySpan) {
                    qtyBox.replace(qtySpan);
                    this.setQtyElementEnabled(key, true);

                    if (updatePrices) {
                        this.setPrice(key);
                    }
                }
            }
        }
    },

    /*
     *  Replace the inline event handlers of the customizable options on a product page.
     */
    replaceInlineEvents: function() {
        var nodeName, nodeType, eventType, inlineEvent, that = this;
        var useAjax     = MP_DDQ_SETTINGS.updateWithAjax;
        var productType = this.productType;

        // {change-container-classname} == Bundle. {super-attribute-select} == Configurable.
        $$('.change-container-classname', '.product-custom-option', '.super-attribute-select').each(function (el) {
            eventType = 'click';
            nodeName  = el.nodeName;
            nodeType  = (nodeName === 'INPUT') ? el.type : '';

            if (nodeName === 'SELECT' || nodeType === 'file') {
                eventType = 'change';
            }
            else if (nodeName === 'TEXTAREA' || nodeType === 'text') {
                eventType = 'blur';
            }

            if (el['on' + eventType]) {
                inlineEvent          = el['on' + eventType];
                el['on' + eventType] = null;
            }

            if (productType === 'bundle' && !el.hasClassName('product-custom-option')) {

                el.observe(eventType, function() {
                    that.setPrice(that.defaultId, this);

                    if (inlineEvent) {
                        inlineEvent(this);
                    }
                });
            }
            else if (productType === 'configurable') {
                el.observe(eventType, function() {
                    var id = that.getProductId() || that.defaultId;

                    if (!useAjax) {
                        that.insertQtyElement(id);
                        that.setPreselected(id);

                        if (typeof that.enabled[id] !== 'undefined' && that.enabled[id]) {
                            that.setPrice(id);
                        }
                        else if (typeof that.enabled[id] === 'undefined') {
                            that.setPrice(that.defaultId);
                        }
                    }
                    else if (useAjax) {
                        if (!that.hasId(id)) {
                            that.ajaxUpdate(id);
                        }
                        else {
                            that.insertQtyElement(id);

                            if (that.enabled[id]) {
                                that.setPrice(id);
                            }
                        }
                    }

                    if (inlineEvent) {
                        inlineEvent();
                    }
                });
            }
            else {
                el.observe(eventType, function() {
                    that.setPrice(that.defaultId);

                    if (inlineEvent) {
                        inlineEvent();
                    }
                });
            }
        });
    },

    setPrice: function(idNum) {
        var price, key;
        var qtyPrices    = this.getQtyPrices(idNum);
        var priceEls     = this.getPriceElements(idNum);
        var textProperty = (document.body.textContent) ? 'textContent' : 'innerText';

        if (qtyPrices) {
            for (key in priceEls) {
                if (priceEls.hasOwnProperty(key) && priceEls[key]) {
                    price = '';

                    switch (key) {
                        case 'excl'      : price = (qtyPrices.hasOwnProperty('e')) ? qtyPrices.e   : qtyPrices.p;  break;
                        case 'incl'      : price = (qtyPrices.hasOwnProperty('i')) ? qtyPrices.i   : qtyPrices.p;  break;
                        case 'unitExcl'  : price = (qtyPrices.hasOwnProperty('eu')) ? qtyPrices.eu : qtyPrices.pu; break;
                        case 'unitIncl'  : price = (qtyPrices.hasOwnProperty('iu')) ? qtyPrices.iu : qtyPrices.pu; break;
                        case 'price'     : price = qtyPrices.p;  break;
                        case 'unitPrice' : price = qtyPrices.pu; break;
                        default          : price = qtyPrices.p;
                    }

                    priceEls[key][textProperty] = this.formatPrice(price);
                }
            }

            if (this.hasOptions()) {
                this.setCustomOptionPrices(idNum);
            }
        }
    },

    setCustomOptionPrices: function(idNum) {
        var configCopy      = this.copyObject(opConfig.config);
        var oldProductPrice = optionsPrice.productPrice;
        var qtyPrices       = this.getQtyPrices(idNum);

        if (qtyPrices) {
            this.setPriceObjectPrices(opConfig.config, this.getQty(idNum));

            optionsPrice.productPrice    = qtyPrices.p;
            optionsPrice.productOldPrice = (qtyPrices.hasOwnProperty('o')) ? qtyPrices.o : qtyPrices.p;
            optionsPrice.priceInclTax    = (qtyPrices.hasOwnProperty('i')) ? qtyPrices.i : qtyPrices.p;
            optionsPrice.priceExclTax    = (qtyPrices.hasOwnProperty('e')) ? qtyPrices.e : qtyPrices.p;
            opConfig.reloadPrice();

            /*Fix for IE7 where it doesn't set the correct price at {onload}.*/
            setTimeout(function() {
                opConfig.config              = configCopy;
                optionsPrice.productPrice    = oldProductPrice;
            }, 0);
        }
    },

    /*
     *   Sets the price properties of the object passed in based on the quantity selected.
     *
     *   The object passed in is supposed to be one of the objects that are created by Magento nativly,
     *   i.e. {opConfig}, {bundle} and {spConfig}.
     */
    setPriceObjectPrices: function(obj, qty) {
        var isBundle = (this.productType === 'bundle');
        var toString = Object.prototype.toString;

        var recurse = function(obj) {
            for (var key in obj) {
                if (obj.hasOwnProperty(key)) {
                    if (toString.call(obj[key]) === '[object Object]') {
                        recurse(obj[key]);
                    }
                    else if (typeof obj[key] === 'number') {
                        if (isBundle) {
                            if (key === 'price' || key === 'oldPrice' || key === 'priceExclTax' || key === 'priceInclTax' || key === 'excludeTax' || key === 'includeTax' || key === 'priceValue') {
                                obj[key] *= qty;
                            }
                        }
                        else {
                            obj[key] *= qty;
                        }
                    }
                }
            }
        };

        recurse(obj);
    },

    setElements: function() {
        var qtyEl, qtyBox, container, newRadio;
        var that = this;

        this.productIds.each(function(id) {
            container = $('mp-ddq-el-' + id);
            qtyBox = that.getQtyBox(id);

            if (that.productType === 'configurable') {
                that.defaultQtyBox = qtyBox;
                that.setQtyBoxAttributes(id, qtyBox.name, qtyBox.className);
            }

            if (container) {
                qtyEl  = that.getQtyElement(id);

                that.elements[id] = qtyEl;

                if (qtyEl.nodeName === 'SELECT') {
                    qtyEl.name = qtyBox.name;
                }
                else if (qtyEl.nodeName === 'TABLE') {
                    qtyEl.select('input[type=radio]').each(function(radio) {
                        try {
                            // Fix for browsers that can't set the {name} and {checked} attributes of already created radios.
                            newRadio = document.createElement('<input type="radio" name="' + qtyBox.name + '" value="' + radio.value + '"' + ((radio.checked) ? 'checked="checked"' : '') + 'class="' + radio.className + '">');
                            radio.replace(newRadio);
                        }
                        catch (e) {
                            radio.name = qtyBox.name;
                        }
                    });
                }

                qtyEl.className += ' ' + qtyBox.className;
                qtyEl.style.display = 'block';
            }
            else {
                that.elements[id] = qtyBox;
            }
        });
    },

    setObserver: function(idNum) {
        var qtyEl       = this.getQtyElement(idNum);
        var that        = this;
        var updatePrice = MP_DDQ_SETTINGS.priceUpdatesEnabled;
        var reloadCart  = MP_DDQ_SETTINGS.reloadCartOnChange;
        var view        = MP_DDQ_SETTINGS.view;

        if (qtyEl) {
            if (qtyEl.nodeName === 'SELECT') {
                qtyEl.observe('change', function() {
                    if (updatePrice) {
                        that.setPrice(idNum);
                    }

                    if (reloadCart && view === 'cart') {
                        that.reloadCart(this);
                    }
                });
            }
            else if (qtyEl.nodeName === 'TABLE') {
                qtyEl.observe('click', function(e) {
                    var target = e.findElement();

                    if (updatePrice && target.nodeName === 'INPUT' && target.type === 'radio') {
                        that.setPrice(idNum);
                    }

                    if (reloadCart && view === 'cart') {
                        that.reloadCart(this);
                    }
                });
            }
        }
    },

    setQtyElementEnabled: function(idNum, state) {
        var qtyEl = this.getQtyElement(idNum);

        var setState = function(el) {
            if (state) {
                el.removeClassName('disabled');
            }
            else {
                el.addClassName('disabled');
            }

            el.disabled = !state;
        };

        if (qtyEl) {
            if (qtyEl.nodeName === 'TABLE') {
                qtyEl.select('input[type="radio"]').each(function(radio) {
                    setState(radio);
                });
            }
            else {
                setState(qtyEl);
            }
        }
    },

    setEnabled: function() {
        var that = this;

        this.productIds.each(function(id) {
            if (typeof that.enabled[id] === 'undefined') {
                that.enabled[id] = that.enabled[that.defaultId];
            }
        });
    },

    /*
     *  Returns the object that has the price properties for a specified quantity.
     */
    getQtyPrices: function(idNum) {
        var qty, obj, optionsId;

        if (MP_DDQ_SETTINGS.view === 'cart') {
            optionsId = idNum;
        }
        else if (this.qtyOptions[idNum]) {
            optionsId = idNum;
        }
        else {
            optionsId = this.defaultId;
        }

        qty = this.getQty(idNum);
        obj = (!isNaN(qty)) ? this.qtyOptions[optionsId][qty] : null;

        return obj;
    },

    getQty: function(idNum) {
        var id = (this.elements[idNum]) ? idNum : this.defaultId;
        var el = (this.layouts[id] === 'table') ? this.getSelectedTableRadio(id) : this.getQtyElement(id);

        return (el) ? parseFloat(el.value).toFixed(4) : NaN;
    },

    getSelectedTableRadio: function(idNum) {
        var radio = null;
        var el    = this.elements[idNum] || this.elements[this.defaultId];

        el.select('input[type=radio]').each(function(radioEl) {
            if (radioEl.checked) {
                radio = radioEl;
                throw $break;
            }
        });

        return radio;
    },

    getQtyBox: function(idNum) {
        var selector, boxPath, array;

        if (MP_DDQ_SETTINGS.view === 'cart') {
            selector = 'input[name="cart[' + idNum + '][qty]"]';
            boxPath  = MP_DDQ_SETTINGS.qtyBoxPaths.cartForm + ' ' + selector;
        }
        else if (this.productType === 'grouped') {
            selector = 'input[name=super_group[' + idNum + ']]';
        }
        else {
            boxPath  = MP_DDQ_SETTINGS.qtyBoxPaths[this.productType];
            selector = 'input[name=qty]';
        }

        array = $$(selector);

        if (!array.length) {
            array = $$(boxPath);
        }

        return array[0];
    },

    getQtyElement: function(idNum) {
        var layout = this.layouts[idNum] || this.layouts[this.defaultId];
        var qtyEl  =  $('mp-ddq-el-' + idNum + '-' + layout);

        return qtyEl;
    },

    hasId: function(idNum) {
        var hasProductId = false;

        this.productIds.each(function(storedId) {
            if (idNum === storedId) {
                hasProductId = true;
                throw $break;
            }
        });

        return hasProductId;
    },

    formatPrice: function(price) {
        return formatCurrency(price, MP_DDQ_SETTINGS.priceFormat);
    },

    reloadCart: function(qtyEl) {
        qtyEl.form.submit();
    },

    destroy: function() {
        for (var key in this) {
            if (this.hasOwnProperty(key)) {
                delete this[key];
            }
        }
    }

});
