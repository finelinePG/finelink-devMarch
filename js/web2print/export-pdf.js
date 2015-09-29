if (!window.chili) {
    window.chili = {};
}

/**
 * Creates a new instance of the pdfLoader
 *
 * @param endpoint
 * @param orderItemId
 * @param exportType
 */
window.chili.pdfLoader = function(endpoint, itemType, itemId, exportType) {

    this.endpoint = endpoint;
    this.itemType = itemType;
    this.itemId = itemId;
    this.exportType = exportType;
    this.pdfTaskId = '';

    /**
     * Add basic parameters to adjust the behaviour of this plugin
     */
    this.defaults = {
        overlay: {
            width: 800,
            height: 100
        },
        listenerInterval: 1000
    };

    /**
     * Start generation of PDF
     */
    this.load = function () {
        var base = this;
        var url = this.endpoint + 'exportpdf';
        var data = this.generateAjaxData({itemType: this.itemType, itemId: this.itemId, exportType: this.exportType});
        new Ajax.Request(url, {
            type: 'POST',
            parameters: data,
            onComplete: function (transport) {
                var responseArray = transport.responseText.evalJSON();
                base.openOverlay(responseArray['content']);
                if(responseArray['status'] == "success"){
                    base.pdfTaskId = responseArray['pdfTaskId'];
                    base.planListener();
                }
            }
        });
    }

    /**
     * Parse object to a data string
     *
     * @param {object|array} data
     * @returns {string}
     */
    this.generateAjaxData = function(data) {
        var res = [];
        for (var i in data) {
            res.push(i + '=' + data[i]);
        }
        return '&' + res.join('&');
    }

    /**
     * This function opens up a popup for the pdf download
     *
     * @param {string} data
     */
    this.openOverlay = function(data) {
        jQuery.colorbox({
                width: this.defaults.overlay.width,
                height:this.defaults.overlay.height,
                html:data}
        );
    }

    /**
     * Create time-based listen functions to check if the pdf is allready created
     */
    this.planListener = function() {
        setTimeout(this.startListener(), 1000); // this.defaults.listenerInterval
    }

    /**
     * Start to listen if a PDF is fully created
     */
    this.startListener = function() {
        var base = this;
        var url = this.endpoint + 'exportpdfstatus';
        var data = this.generateAjaxData({pdfTaskId: this.pdfTaskId, itemType: this.itemType, itemId: this.itemId, exportType: this.exportType});
        new Ajax.Request(url, {
            type: 'POST',
            parameters: data,
            onComplete: function (transport) {
                var responseArray = transport.responseText.evalJSON();
                if(responseArray['status'] == "pending"){
                    base.planListener();
                }

                if (responseArray['status'] == 'success') {
                    base.openOverlay(responseArray['content']);
                }

                if (responseArray['status'] == 'error' || responseArray['status'] == null) {
                    base.openOverlay(responseArray['error']);
                }
            }
        });
    }
}

/**
 * Prototype:
 * Initialize the pdf exports based on the className
 */
document.observe('dom:loaded', function() {
    $$('.product-download-pdf').each(function (obj) {
        obj.observe('click', function(e) {
            e.preventDefault();

            var loader = new window.chili.pdfLoader(this.readAttribute('data-endpoint'), this.readAttribute('data-itemtype'), this.readAttribute('data-itemid'), this.readAttribute('data-exporttype'));

            loader.load();
        });
    });
});

