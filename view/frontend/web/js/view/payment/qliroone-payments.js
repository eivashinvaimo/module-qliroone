/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';
 
        rendererList.push(
            {
                type: 'qliroone',
                component: 'Qliro_QliroOne/js/view/payment/method-renderer/qliroone-method'
            }
        );
 
        /** Add view logic here if needed */
        return Component.extend({});
    });
 
