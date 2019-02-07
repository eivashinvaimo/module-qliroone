/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

// @codingStandardsIgnoreFile
// phpcs:ignoreFile

define([
    'ko',
    'uiComponent',
    'underscore',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/model/quote',
    'Qliro_QliroOne/js/model/qliro',
    'Qliro_QliroOne/js/model/config'
], function (
    ko,
    Component,
    _,
    stepNavigator,
    shippingService,
    customerData,
    quote,
    qliro,
    config
) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'Qliro_QliroOne/checkout/onepage',
            imports: {
                discountApplied: 'checkout.steps.billing-step.discount:isApplied'
            },
        },
        discountApplied: ko.observable(false),

        isVisible: ko.observable(true),

        initialize: function () {
            this._super();

            this.initializeSubscribers();
            this.initializeQliro();

            // if (!quote.isVirtual()) {
            //     stepNavigator.registerStep('shipping', null, 'Shipping', false, _.bind(this.navigate, this), 0);
            // }

            stepNavigator.registerStep(
                'qliroone-step',
                null,
                config.checkoutTitle,
                this.isVisible,
                _.bind(this.navigate, this),
                100
            );

            return this;
        },

        initializeSubscribers: function() {
            this.discountApplied.subscribe(function() {
                qliro.updateCart();
            });
        },

        initializeQliro: function() {
            window.q1Ready = function(q1) {
                console.log(q1); // Debugging
                q1.onCheckoutLoaded(qliro.onCheckoutLoaded);
                q1.onCustomerInfoChanged(qliro.onCustomerInfoChanged);
                q1.onPaymentDeclined(qliro.onPaymentDeclined);
                q1.onPaymentMethodChanged(qliro.onPaymentMethodChanged);
                q1.onPaymentProcess(qliro.onPaymentProcess);
                q1.onSessionExpired(qliro.onSessionExpired);
                q1.onShippingMethodChanged(qliro.onShippingMethodChanged);
                q1.onShippingPriceChanged(qliro.onShippingPriceChanged);
            }
        },

        /**
         * The navigate() method is responsible for navigation between checkout step
         * during checkout. You can add custom logic, for example some conditions
         * for switching to your custom step. (This method is required even though it
         * is blank, don't delete)
         */
        navigate: function () {
        },

        navigateToNextStep: function () {
            stepNavigator.next();
        }
    });
});
