/**
 * Created by BBQJohan on 2014-03-27.
 */
MageParts.Ddq.ProductList = Class.create(MageParts.Ddq.Product, {
    initialize: function($super, config) {
        $super(config);
    },

    insertLayoutElements: function() {
        var productId = this.productIds[0];
        var oldQtyEl  = $('qty-box-' + productId);
        var qtyEl     = $('mp-ddq-el-' + productId + '-select');

        if (oldQtyEl && qtyEl) {
            oldQtyEl.replace(qtyEl);

            this.setPrice(productId);
            this.setQtyElementEnabled(productId, true);
        }
    },

    setElements: function() {
        var productId = this.productIds[0];
        var oldQtyEl  = $('qty-box-' + productId);
        var qtyEl     = $('mp-ddq-el-' + productId + '-select');
        qtyEl.name    = oldQtyEl.name;

        if (oldQtyEl) {
            this.elements[productId] = qtyEl;
            qtyEl.style.display = 'block';
        }
    }
});