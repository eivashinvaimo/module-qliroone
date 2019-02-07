<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * Update Shipping Methods notification callback response interface
 */
interface UpdateShippingMethodsResponseInterface extends ContainerInterface
{
    const REASON_POSTAL_CODE = 'PostalCodeIsNotSupported';

    /**
     * @return \Qliro\QliroOne\Api\Data\QliroOrderShippingMethodInterface[]
     */
    public function getAvailableShippingMethods();

    /**
     * @return string
     */
    public function getDeclineReason();

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderShippingMethodInterface[] $value
     * @return $this
     */
    public function setAvailableShippingMethods($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setDeclineReason($value);
}
