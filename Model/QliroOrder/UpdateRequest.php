<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder;

use Qliro\QliroOne\Api\Data\QliroOrderUpdateRequestInterface;

/**
 * QliroOne Order Update Request concrete implementation
 */
class UpdateRequest implements QliroOrderUpdateRequestInterface
{
    /**
     * @var string
     */
    private $orderItems;

    /**
     * @var string
     */
    private $availableShippingMethods;

    /**
     * @var string
     */
    private $requireIdentityVerification;

    /**
     * @return \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[]
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * @return string
     */
    public function getAvailableShippingMethods()
    {
        return $this->availableShippingMethods;
    }

    /**
     * @return bool
     */
    public function getRequireIdentityVerification()
    {
        return $this->requireIdentityVerification;
    }

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[] $value
     * @return $this
     */
    public function setOrderItems($value)
    {
        $this->orderItems = $value;

        return $this;
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setAvailableShippingMethods($value)
    {
        $this->availableShippingMethods = $value;

        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setRequireIdentityVerification($value)
    {
        $this->requireIdentityVerification = $value;

        return $this;
    }
}