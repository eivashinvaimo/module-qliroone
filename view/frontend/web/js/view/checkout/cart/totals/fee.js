/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

define(
    [
        'Qliro_QliroOne/js/view/checkout/summary/fee',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'ko'
    ],
    function (Component, quote, priceUtils, ko) {
        'use strict';

        return Component.extend({
            showPaymentFee: ko.observable(false),

            getValue: function() {
                var price = null,
                    paymentFee;

                if (this.totals()) {
                    price = this.getTotalsFeeValue();
                }

                paymentFee = (price || this.getTitle()) ? priceUtils.formatPrice(price, quote.getBasePriceFormat()) : null;

                if (paymentFee) {
                    this.showPaymentFee(true);
                } else {
                    this.showPaymentFee(false);
                }

                return (price || this.getTitle()) ? this.getFormattedPrice(price) : null;
            }
        });
    }
);
