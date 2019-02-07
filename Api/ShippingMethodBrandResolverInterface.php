<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api;

use Magento\Quote\Model\Quote\Address\Rate;

/**
 * Shipping Method Brand Resolver interface
 */
interface ShippingMethodBrandResolverInterface
{
    /**
     * Resolve the brand name for the shipping method.
     * The following logotypes are currently supported in Qliro One: Aramex, Best, Bring, Budbee, DHL, Instabox,
     * MTD, Posti, PostNord, Schenker, UPS.
     *
     * @param \Magento\Quote\Model\Quote\Address\Rate $shippingRate
     * @return string
     */
    public function resolve(Rate $shippingRate);
}
