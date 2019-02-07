/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, quote, priceUtils, totals) {
        "use strict";
        return Component.extend({
            defaults: {
                isFullTaxSummaryDisplayed: window.checkoutConfig.isFullTaxSummaryDisplayed || false,
                template: 'Qliro_QliroOne/checkout/summary/fee'
            },
            totals: quote.getTotals(),
            isTaxDisplayedInGrandTotal: window.checkoutConfig.includeTaxInGrandTotal || false,
            isDisplayed: function() {
                return this.isFullMode();
            },
            getValue: function() {
                var price = null;
                if (this.totals()) {
                    price = this.getTotalsFeeValue();
                }
                return (price || this.getTitle()) ? this.getFormattedPrice(price) : null;
            },
            getTotalsFeeValue: function() {
                var totalsPaymentFeeSegment = totals.getSegment('qliroone_fee');
                return totalsPaymentFeeSegment ? totalsPaymentFeeSegment.value : null;
            },
            getBaseValue: function() {
                var price = null;
                if (this.totals()) {
                    price = this.totals().base_payment_charge;
                }
                return (price || this.getTitle()) ? priceUtils.formatPrice(price, quote.getBasePriceFormat()) : null;
            },
            getTitle: function() {
                if (this.totals()) {
                    var totalSegments = this.totals().total_segments;
                    if (totalSegments) {
                        for (var i = 0; i < totalSegments.length; i++) {
                            if (totalSegments[i].code == 'qliroone_fee' && totalSegments[i].title) {
                                return totalSegments[i].title;
                            }
                        }
                    }
                }
                var feeSection = window.checkoutConfig.qliro.qliroone_fee;
                return feeSection ? feeSection.fee_setup.default.description : null;
            },
        });
    }
);