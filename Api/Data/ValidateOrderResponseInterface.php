<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * Validation notification callback response interface
 */
interface ValidateOrderResponseInterface extends ContainerInterface
{
    const REASON_OUT_OF_STOCK = 'OutOfStock';
    const REASON_POSTAL_CODE = 'PostalCodeIsNotSupported';
    const REASON_SHIPPING = 'ShippingIsNotSupportedForPostalCode';
    const REASON_CASH_ON_DELIVERY = 'CashOnDeliveryIsNotSupportedForShippingMethod';
    const REASON_IDENTITY_VERIFICATION = 'IdentityNotVerified';
    const REASON_OTHER = 'Other';

    /**
     * @return string
     */
    public function getDeclineReason();

    /**
     * @param string $value
     * @return $this
     */
    public function setDeclineReason($value);
}
