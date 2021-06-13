(function($) {

    if (typeof Craft.Pricelists === 'undefined') {
        Craft.Pricelists = {};
    }

    Craft.Pricelists.ProductEdit = Garnish.Base.extend({
        rowHtml: '',
        products: null,
        totalNewRows: 0,

        $container: null,
        $productContainer: null,
        $productRows: null,
        $addBtn: null,

        init: function(id, products, rowHtml) {
            this.rowHtml = rowHtml;
            this.products = products;
            this.$container = $('#' + id);

            this.$productContainer = this.$container.find('.create-pricelist-products-container');
            this.$productRows = this.$productContainer.find('.create-pricelist-product');
            this.$addBtn = this.$container.find('.add-pricelist-product');

            for (var i = 0; i < this.$productRows.length; i++) {
                var id = $(this.$productRows[i]).data('id');

                new Craft.Pricelists.ProductEditRow(this, this.$productRows[i], i);

                var newMatch = (typeof id === 'string' && id.match(/new(\d+)/));

                if (newMatch && newMatch[1] > this.totalNewRows) {
                    this.totalNewRows = parseInt(newMatch[1]);
                }
            }

            this.addListener(this.$addBtn, 'click', 'addProduct');
        },

        addProduct: function() {
            this.totalNewRows++;

            let id = 'new' + this.totalNewRows;

            let bodyHtml = this.getParsedBlockHtml(this.rowHtml.bodyHtml, id),
                footHtml = this.getParsedBlockHtml(this.rowHtml.footHtml, id);

            let $newRow = $(bodyHtml).appendTo(this.$productContainer);

            Garnish.$bod.append(footHtml);

            Craft.initUiElements($newRow);

            new Craft.Pricelists.ProductEditRow(this, $newRow, id);
        },

        getParsedBlockHtml: function(html, id) {
            if (typeof html == 'string') {
                return html.replace(/__ROWID__/g, id);
            } else {
                return '';
            }
        },
    });

    Craft.Pricelists.ProductEditRow = Garnish.Base.extend({
        id: null,
        editContainer: null,

        $container: null,

        $productTypeFields: null,
        $deleteBtn: null,
        $originalPrice: null,

        init: function(editContainer, row, id) {
            this.id = id;
            this.editContainer = editContainer;

            this.$container = $(row);

            this.$elementSelect = this.$container.find('.elementselect');
            this.$deleteBtn = this.$container.find('.delete.icon.button');
            this.$originalPrice = this.$container.find('.pricelist-original-price');

            // Wait until the element select field is ready
            Garnish.requestAnimationFrame($.proxy(function() {
                var elementSelect = this.$elementSelect.data('elementSelect');

                // Attach an on-select and on-remove handler
                elementSelect.settings.onSelectElements = $.proxy(this, 'onSelectElements');
                elementSelect.settings.onRemoveElements = $.proxy(this, 'onRemoveElements');
            }, this));

            this.addListener(this.$deleteBtn, 'click', 'deleteRow');
        },

        onSelectElements: function(elements) {
            this.$originalPrice.val(($(elements[0].$element[0]).data('price')));
        },

        onRemoveElements: function(elements) {
            this.$originalPrice.val(null);
        },

        deleteRow: function() {
            var deleteRow = confirm(Craft.t('pricelists', 'Are you sure you want to delete this pricelist product?'));

            if (deleteRow) {
                this.$container.remove();
            }
        },
    });

    Craft.Pricelists.CustomerEdit = Garnish.Base.extend({
        rowHtml: '',
        customers: null,
        totalNewRows: 0,

        $container: null,
        $customerContainer: null,
        $customerRows: null,
        $addBtn: null,

        init: function(id, customers, rowHtml) {
            this.rowHtml = rowHtml;
            this.customers = customers;
            this.$container = $('#' + id);

            this.$customerContainer = this.$container.find('.create-pricelist-products-container');
            this.$customerRows = this.$customerContainer.find('.create-pricelist-product');
            this.$addBtn = this.$container.find('.add-pricelist-customer');

            for (var i = 0; i < this.$customerRows.length; i++) {
                var id = $(this.$customerRows[i]).data('id');

                new Craft.Pricelists.CustomerEditRow(this, this.$customerRows[i], i);

                var newMatch = (typeof id === 'string' && id.match(/new(\d+)/));

                if (newMatch && newMatch[1] > this.totalNewRows) {
                    this.totalNewRows = parseInt(newMatch[1]);
                }
            }

            this.addListener(this.$addBtn, 'click', 'addCustomer');
        },

        addCustomer: function() {
            this.totalNewRows++;

            let id = 'new' + this.totalNewRows;

            let bodyHtml = this.getParsedBlockHtml(this.rowHtml.bodyHtml, id),
                footHtml = this.getParsedBlockHtml(this.rowHtml.footHtml, id);

            let $newRow = $(bodyHtml).appendTo(this.$customerContainer);

            Garnish.$bod.append(footHtml);

            Craft.initUiElements($newRow);

            new Craft.Pricelists.CustomerEditRow(this, $newRow, id);
        },

        getParsedBlockHtml: function(html, id) {
            if (typeof html == 'string') {
                return html.replace(/__ROWID__/g, id);
            } else {
                return '';
            }
        },
    });

    Craft.Pricelists.CustomerEditRow = Garnish.Base.extend({
        id: null,
        editContainer: null,

        $container: null,

        $deleteBtn: null,

        init: function(editContainer, row, id) {
            this.id = id;
            this.editContainer = editContainer;

            this.$container = $(row);

            this.$elementSelect = this.$container.find('.elementselect');
            this.$deleteBtn = this.$container.find('.delete.icon.button');

            // Wait until the element select field is ready
            Garnish.requestAnimationFrame($.proxy(function() {
                var elementSelect = this.$elementSelect.data('elementSelect');

                // Attach an on-select and on-remove handler
                // elementSelect.settings.onSelectElements = $.proxy(this, 'onSelectElements');
                // elementSelect.settings.onRemoveElements = $.proxy(this, 'onRemoveElements');
            }, this));

            this.addListener(this.$deleteBtn, 'click', 'deleteRow');
        },

        onSelectElements: function(elements) {
            this.$originalPrice.val(($(elements[0].$element[0]).data('price')));
        },

        onRemoveElements: function(elements) {
            this.$originalPrice.val(null);
        },

        deleteRow: function() {
            var deleteRow = confirm(Craft.t('pricelists', 'Are you sure you want to delete this pricelist customer?'));

            if (deleteRow) {
                this.$container.remove();
            }
        },
    });

})(jQuery);
