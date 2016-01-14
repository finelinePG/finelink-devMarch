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
MageParts.Ddq.Configurable = Class.create(MageParts.Ddq.Product, {
    initialize: function($super, obj) {
        $super(obj);

        this.currentQtyEl     = null;
        this.currentQtyId     = null;
        this.defaultQtyBox    = null;
        this.qtyBoxAttributes = {};
        this.ajaxLoader       = null;
    },

    insertLayoutElements: function() {
        var productId     = this.getProductId();
        var id            = (this.elements[productId]) ? productId : this.defaultId;
        var el            = $$('input[name=qty]')[0];
        var replacementEl = this.elements[id];

        if (el) {
            el.replace(replacementEl);

            if (replacementEl.style.display !== 'block') {
                replacementEl.style.display = 'block';
            }

            this.currentQtyId = id;
            this.currentQtyEl = replacementEl;
            this.setQtyElementEnabled(id, true);
            this.setPreselected(id);
            this.setPrice(id);
        }
    },

    insertQtyElement: function(idNum) {
        var id        = (this.elements[idNum]) ? idNum : this.defaultId;
        var el        = this.elements[id];
        var currentEl = this.elements[this.currentQtyId];

        if (el && el !== currentEl) {
            currentEl.replace(el);

            if (el.style.display !== 'block') {
                el.style.display = 'block';
            }

            this.setQtyElementEnabled(this.currentQtyId, false);
            this.setQtyElementEnabled(id, true);
            this.currentQtyId = id;
        }
    },

    setPreselected: function(idNum)  {
        var layout = this.layouts[idNum] || this.layouts[this.defaultId];
        var el     = this.getQtyElement(idNum);
        var value  = this.preselected[idNum] + '';

        if (el && value && el !== this.defaultQtyBox) {
            if (layout === 'table') {
                el.select('input[type=radio]').each(function(radio) {
                    if (radio.value === value) {
                        radio.checked = true;
                        throw $break;
                    }
                });
            }
            else if (layout === 'select') {
                el.selected = true;
            }
        }
    },

    setPrice: function(idNum) {
        var option;
        var qtyPrices  = this.getQtyPrices(idNum);
        var qty        = this.getQty(idNum);
        var oldPrices  = [];

        if (qtyPrices && qty) {
            if (this.hasOptions()) {
                this.setCustomOptionPrices(idNum);
            }

            optionsPrice.productPrice    = qtyPrices.p;
            optionsPrice.productOldPrice = (qtyPrices.hasOwnProperty('o')) ? qtyPrices.o : qtyPrices.p;
            optionsPrice.priceInclTax    = (qtyPrices.hasOwnProperty('i')) ? qtyPrices.i : qtyPrices.p;
            optionsPrice.priceExclTax    = (qtyPrices.hasOwnProperty('e')) ? qtyPrices.e : qtyPrices.p;
            optionsPrice.reload();

            spConfig.settings.each(function(el) {
                option = el.options[el.selectedIndex];

                if (option.selected && option.getAttribute('price') !== null) {
                    oldPrices.push({
                        element: option,
                        price: option.config.price
                    });

                    option.config.price *= qty;
                }
            });

            spConfig.reloadPrice();

            /*
             *  In IE 7/8 the price won't update correctly if this timeout is not used.
             *  Without it the additional configured price is added to the total instead of multiplied with the qty and then added.
             */
            setTimeout(function() {
                oldPrices.each(function(obj) {
                    obj.element.config.price = obj.price;
                });

                // In IE 7/8 the prices on the labels of the options will be worng unless they are updated like this.
                spConfig.settings.each(function(el) {
                    spConfig.reloadOptionLabels(el);
                });
            }, 0);
        }
    },

    getProductId: function() {
        var key, innerKey, currentSimpleProductId, selected;
        var existingProducts       = {};
        var sizeOfExistingProducts = 0;

        spConfig.settings.each(function(selectEl) {
            selected = selectEl.options[selectEl.selectedIndex];

            if (selected.config) {
                selected.config.products.each(function(id) {
                    if (!existingProducts[id]) {
                        existingProducts[id] = 1;
                    }
                    else {
                        existingProducts[id] += 1;
                    }
                });
            }
        });

        for (key in existingProducts) {
            for (innerKey in existingProducts) {
                if (+existingProducts[innerKey] < +existingProducts[key]) {
                    delete existingProducts[innerKey];
                }
            }
        }

        for (key in existingProducts) {
            currentSimpleProductId = key;
            sizeOfExistingProducts += 1;
        }

        if (sizeOfExistingProducts !== 1) {
            currentSimpleProductId = null;
        }

        return currentSimpleProductId;
    },

    ajaxUpdate: function(idNum) {
        var that = this;

        this.toggleAjaxLoader(true);

        new Ajax.Request(MP_DDQ_SETTINGS.baseUrl + 'ddq/index/fetchForConfigurable/mp_ddq_configurable_id/' + this.defaultId + '/mp_ddq_simple_id/' + idNum, {
            onSuccess: function(response) {
                var config  = response.responseJSON.config;

                that.toggleAjaxLoader(false);

                that.enabled[idNum] = config.enabled;
                that.productIds.push(idNum);

                if (config.enabled) {
                    if (config.qtyOptions) {
                        that.qtyOptions[idNum]  = config.qtyOptions;
                    }

                    if (config.layout) {
                        that.layouts[idNum]  = config.layout;
                    }

                    if (config.preselected) {
                        that.preselected[idNum]  = config.preselected;
                    }

                    if (response.responseJSON.html) {
                        that.setAjaxElement(idNum, response.responseJSON.html, config.layout);
                        that.insertQtyElement(idNum);
                        that.setObserver(idNum);
                    }
                    else {
                        that.insertQtyElement(idNum);
                    }

                    that.setPrice(idNum);
                }
                else if (!config.enabled) {
                    that.setAjaxElement(idNum);
                    that.insertQtyElement(idNum);
                }
            }
        });
    },

    setAjaxLoader: function() {
        if (!this.ajaxLoader) {
            this.ajaxLoader = $('mp-ddq-ajax-loader');

            if (this.ajaxLoader) {
                this.elements[this.currentQtyId].insert({before: this.ajaxLoader});
            }
        }
    },

    toggleAjaxLoader: function(state) {
        if (this.ajaxLoader) {
            if (state) {
                this.ajaxLoader.style.display = 'block';
                this.elements[this.currentQtyId].style.display = 'none';
            }
            else {
                this.ajaxLoader.style.display = 'none';
                this.elements[this.currentQtyId].style.display = 'block';
            }
        }
    },

    setAjaxElement: function(idNum, html, layout) {
        var container, qtyEl, tempEl, qtyBoxAttrs;

        if (this.enabled[idNum]) {
            qtyBoxAttrs = this.qtyBoxAttributes[this.defaultId];

            tempEl = new Element('div');
            tempEl.innerHTML = html;

            qtyEl     = tempEl.select('#mp-ddq-el-' + idNum + '-' + layout)[0];
            container = tempEl.select('#mp-ddq-el-' + idNum)[0];

            this.setQtyElAttributes(qtyEl);

            qtyEl.className += ' ' + qtyBoxAttrs['class'];
            qtyEl.style.display = 'block';

            this.elements[idNum] = container;
        }
        else {
            this.elements[idNum] = this.defaultQtyBox;
        }
    },

    setQtyBoxAttributes: function(idNum, name, className) {
        this.qtyBoxAttributes[idNum] = {
            name: name,
            'class': className
        };
    },

    setQtyElAttributes: function(qtyEl) {
        var newRadio;
        var qtyBoxAttrs = this.qtyBoxAttributes[this.defaultId];

        if (qtyEl.nodeName === 'SELECT') {
            qtyEl.name = qtyBoxAttrs.name;
        }
        else if (qtyEl.nodeName === 'TABLE') {
            qtyEl.select('input[type=radio]').each(function(radio) {
                try {
                    // Fix for browsers that can't set the {name} and {checked} attributes of already created radios.
                    newRadio = document.createElement('<input type="radio" name="' + qtyBoxAttrs.name + '" value="' + radio.value + '"' + ((radio.checked) ? 'checked="checked"' : '') + 'class="' + radio.className + '">');
                    radio.replace(newRadio);
                }
                catch (e) {
                    radio.name = qtyBoxAttrs.name;
                }
            });
        }
    }
});
