/**
 * Created by BBQJohan on 2014-03-28.
 */
MageParts.Ddq.Cart = Class.create(MageParts.Ddq.Product, {
    initialize: function($super, obj) {
        $super(obj);
    },

    setElements: function() {
        var productId = this.productIds[0];
        var oldQtyEl  = this.getQtyBox(productId);
        var qtyEl     = $('mp-ddq-el-' + productId + '-select');

        if (oldQtyEl) {
            this.elements[productId] = qtyEl;

            qtyEl.style.display = 'block';
            qtyEl.name = oldQtyEl.name;
        }
    }
}); 