/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

define(
    [
        'Qliro_QliroOne/js/view/cart/summary/fee'
    ],
    function (Component) {
        'use strict';

        return Component.extend({

            /**
             * @override
             *
             * @returns {boolean}
             */
            isDisplayed: function () {
                return true;
            }
        });
    }
);
