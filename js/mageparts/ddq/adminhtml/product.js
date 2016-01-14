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

    /**
     * Whether or not the extension is enabled.
     *
     * @var boolean
     */
    enabled: true,

    /**
     * Row template holder.
     *
     * @var string
     */
    rowTemplate: null,

    /**
     * Keeps count of number of rows.
     *
     * @var int
     */
    rowCount: 0,

    /**
     * Whether or not we are working from the attribute updater page.
     *
     * @var boolean
     */
    isOnUpdateAttributePage: false,

    /**
     * Whether or not to apply read only to table data.
     *
     * @var boolean
     */
    readOnly: false,

    /**
     * Quantity Options Enabled label (replaces the original "Enabled")
     * on the update attributes page. Otherwise it's confusing
     * which function this setting enables / disables.
     *
     * @var string
     */
    ddqEnabledLabel: 'Quantity Options Enabled',

    /**
     * Current store view id. Used as a comparison check to set accurate
     * label for use config / default checkboxes.
     *
     * @param int
     */
    storeId: 0,

    /**
     * Keeps information regarding which ddq related values are
     * specific for this product.
     *
     * @param object
     */
    useConfig: {},

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
     * Initiate form after page is done loading.
     *
     * @return this
     */
    initForm: function()
    {
        var targetEls = ['ddq_enabled', 'ddq_qty_list', 'ddq_preselected', 'ddq_incremental', 'ddq_hide_unavailable_qty', 'ddq_layout'];

        if (this.enabled) {
            if (this.isOnUpdateAttributePage) {
                var inventoryTab = $('attributes_update_tabs_inventory_content');

                if (inventoryTab && inventoryTab.nodeType) {
                    inventoryTab.insert({bottom: $('mp-ddq-form')});
                }
            }

            var destinationEl = $('ddq').down('tbody');

            if (destinationEl && destinationEl.nodeType) {
                for (var i=0; i<targetEls.length; i++) {
                    var targetElId = targetEls[i];
                    var targetEl = $(targetElId).up('tr');

                    if (this.storeId === 0 && !this.isOnUpdateAttributePage) {
                        var useConfig = this.useConfig[targetElId];

                        var useConfigHtml = '<input type="checkbox" name="ddq_use_config[' + targetElId + ']" id="' + targetElId + '_use_config" ' + (useConfig ? 'checked="checked" ' : '') + 'onclick="toggleValueElements(this, this.parentNode);" class="checkbox" />';
                        useConfigHtml+= '<label for="' + targetElId + '_use_config" class="normal">Use Config Settings</label>';

                        if (targetElId !== 'ddq_qty_list') {
                            var valueTd = targetEl.down('td.value');
                            valueTd.insert({bottom: useConfigHtml});
                        } else {
                            $('ddq_qty_list').up('td').insert({bottom: useConfigHtml});
                        }

                        if (useConfig) {
                            if (targetElId !== 'ddq_qty_list') {
                                $(targetElId).disabled = true;
                            }
                        }
                    }

                    if (targetEl) {
                        destinationEl.insert({bottom: targetEl});
                    }
                }
            }
        } else {
            $('mp-ddq-form').remove();

            for (var i=0; i<targetEls.length; i++) {
                var targetEl = $(targetEls[i]).up('tr');

                if (targetEl && targetEl.nodeType) {
                    targetEl.remove();
                }
            }
        }

        return this;
    },

    /**
     * Add row to table.
     *
     * @return this
     */
    addRow: function()
    {
        if (this.enabled) {
            // keep object instance reference
            var that = this;

            // row data
            var data = {
                qty: '',
                price: '',
                label: '',
                order: '',
                readOnly: this.readOnly,
                index: this.rowCount++
            };

            // assign row data based on incoming function call arguments
            if(arguments.length > 3) {
                data.qty = arguments[0];
                data.price = arguments[1];
                data.label  = arguments[2];
                data.order = arguments[3];

                if (arguments.length == 5) {
                    data.readOnly = arguments[4];
                }
            }

            // insert row HTML
            Element.insert($('ddq_qty_list'), {
                bottom : this.rowTemplate.evaluate(data)
            });

            // apply read only
            var useDefaultValueCheckbox = $('ddq_qty_list_default');
            var useDefaultCheckbox = $('ddq_qty_list_use_config');

            if (!this.isOnUpdateAttributePag && data.readOnly || (useDefaultCheckbox && useDefaultCheckbox.checked) || (useDefaultValueCheckbox && useDefaultValueCheckbox.checked)) {
                this.toggle(false);
            } else {
                $('ddq_qty_list').select('input', 'select').each(function(el){ Event.observe(el, 'change', el.setHasChanges.bind(el)); });
            }
        }

        return this;
    },

    /**
     * Toggle table elements (enabled / disabled).
     *
     * @param enabled boolean
     * @return this
     */
    toggle: function(enabled)
    {
        if (this.enabled && !this.isOnUpdateAttributePage) {
            var inputElCollection = $('ddq_qty_list').select('input', 'select');
            var buttonCollection = $('ddq_qty_list').up('table').select('button');

            var that = this;

            if (inputElCollection.length) {
                inputElCollection.each(function(el) {
                    that.toggleElement(el, enabled);
                });
            }

            if (buttonCollection.length) {
                buttonCollection.each(function(el) {
                    that.toggleElement(el, enabled);
                });
            }
        }

        return this;
    },

    /**
     * Toggles an element (enabled / disabled).
     *
     * @param el Element
     * @param enabled boolean
     * @return this
     */
    toggleElement: function(el, enabled)
    {
        if (this.enabled && !this.isOnUpdateAttributePage) {
            el.disabled = !enabled;

            if (enabled) {
                el.removeClassName('disabled');
            } else {
                el.addClassName('disabled');
            }
        }

        return this;
    },

    /**
     * Delete a table row.
     *
     * @param event
     * @return this
     */
    deleteRow: function(event)
    {
        if (this.enabled) {
            var tr = Event.findElement(event, 'tr');

            if (tr) {
                tr.remove();
            }
        }

        return this;
    }

});
