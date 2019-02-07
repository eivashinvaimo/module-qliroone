<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Notification;

use Qliro\QliroOne\Api\Data\UpdateShippingMethodsResponseInterface;

/**
 * ShippingMethodsResponse class
 */
class ShippingMethodsResponse implements UpdateShippingMethodsResponseInterface
{
    /**
     * @var string
     */
    private $declineReason;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderShippingMethodInterface[]
     */
    private $availableShippingMethods;

    /**
     * @return \Qliro\QliroOne\Api\Data\QliroOrderShippingMethodInterface[]
     */
    public function getAvailableShippingMethods()
    {
        return $this->availableShippingMethods;
    }

    /**
     * @return string
     */
    public function getDeclineReason()
    {
        return $this->declineReason;
    }

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderShippingMethodInterface[] $value
     * @return $this
     */
    public function setAvailableShippingMethods($value)
    {
        $this->availableShippingMethods = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDeclineReason($value)
    {
        $this->declineReason = $value;

        return $this;
    }
}
