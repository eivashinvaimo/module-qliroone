/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

// @codingStandardsIgnoreFile
// phpcs:ignoreFile

define([
    'jquery',
    'Qliro_QliroOne/js/model/config',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/cart/totals-processor/default',
    'Magento_Checkout/js/model/cart/cache',
    'Magento_Customer/js/customer-data',
    'mage/translate'
], function(
    $,
    config,
    quote,
    totalsProcessor,
    cartCache,
    customerData,
    __
) {
    function sendUpdateQuote() {
        return (
            $.ajax({
                url: config.updateQuoteUrl + '?quote_id=' + quote.getQuoteId() + '&token=' + config.securityToken,
                method: 'POST'
            })
        )
    }

    function sendAjaxAsJson(url, data) {
        return $.ajax({
            url: url + '?token=' + config.securityToken,
            method: 'POST',
            data: JSON.stringify(data),
            processData: false,
            contentType: 'application/json'
        });
    }

    function showErrorMessage(message) {
        customerData.set('messages', {
            messages: [{
                type: 'error',
                text: message
            }]
        });
    }

    function qliroDebug(caption, data) {
        if (config.isDebug) {
            console.log(caption, data);
        }
    }

    function qliroSuccessDebug(caption, data) {
        qliroDebug('Success: ' + caption, data);
    }

    function updateTotals() {
        cartCache.set('totals', null);
        totalsProcessor.estimateTotals(quote.shippingAddress());
    }

    return {
        updateCart: function() {
            if (!config.isEagerCheckoutRefresh) {
                window.q1.lock();
            } else {
                qliroDebug('Skipping checkout lock.');
            }

            sendUpdateQuote()
                .then(
                    function(data) {
                        var unmatchCount = 0;

                        window.q1.onOrderUpdated(function(order) {
                            if (config.isEagerCheckoutRefresh) {
                                qliroDebug('Skipping checkout update polling.');

                                return true;
                            }

                            if (Math.abs(order.totalPrice - data.order.totalPrice) < 0.005) {
                                unmatchCount = 0;
                                window.q1.unlock();
                            } else {
                                unmatchCount++;

                                if (unmatchCount > 3) {
                                    unmatchCount = 0;
                                    showErrorMessage(__('Store and Qliro One totals don\'t match. Refresh the page.'));
                                }
                            }
                        })
                    },
                    function(response, state, reason) {
                        var data = response.responseJSON || {};

                        if (!config.isEagerCheckoutRefresh) {
                            window.q1.unlock();
                        } else {
                            qliroDebug('Skipping checkout unlock.');
                        }

                        showErrorMessage(data.error || reason);
                    }
                );
        },

        onCheckoutLoaded: function() {
            qliroSuccessDebug('onCheckoutLoaded', q1);
        },

        onCustomerInfoChanged: function(customer) {
            sendAjaxAsJson(config.updateCustomerUrl, customer).then(
                function(data) {
                    qliroSuccessDebug('onCustomerInfoChanged', data);
                },
                function(response) {
                    var data = response.responseJSON || {};
                    var error = data.error || __('Something went wrong while updating customer.');
                    showErrorMessage(error);
                }
            );
        },

        onPaymentDeclined: function(declineReason) {
            qliroSuccessDebug('onPaymentDeclined', declineReason);
        },

        onPaymentMethodChanged: function(paymentMethod) {
            sendAjaxAsJson(config.updatePaymentMethodUrl, paymentMethod).then(
                function(data) {
                    qliroSuccessDebug('onPaymentMethodChanged', data);
                    updateTotals();
                },
                function(response) {
                    var data = response.responseJSON || {};
                    var error = data.error || __('Something went wrong while updating payment method.');
                    showErrorMessage(error);
                }
            );
        },

        onPaymentProcess: function() {
            qliroSuccessDebug('onPaymentProcess', q1);
        },

        onSessionExpired: function() {
            qliroSuccessDebug('onSessionExpired', q1);
        },

        onShippingMethodChanged: function(shipping) {
            sendAjaxAsJson(config.updateShippingMethodUrl, shipping).then(
                function(data) {
                    qliroSuccessDebug('onShippingMethodChanged', data);
                    updateTotals();
                },
                function(response) {
                    var data = response.responseJSON || {};
                    var error = data.error || __('Something went wrong while updating shipping method.');
                    showErrorMessage(error);
                }
            );
        },

        onShippingPriceChanged: function(newShippingPrice) {
            sendAjaxAsJson(config.updateShippingPriceUrl, {newShippingPrice: newShippingPrice}).then(
                function(data) {
                    qliroSuccessDebug('onShippingPriceChanged', data);
                    updateTotals();
                },
                function(response) {
                    var data = response.responseJSON || {};
                    var error = data.error || __('Something went wrong while updating shipping method options.');
                    showErrorMessage(error);
                }
            );
        }
    }
});

