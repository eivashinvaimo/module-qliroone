<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Notification;

use Qliro\QliroOne\Api\Data\CheckoutStatusInterface;

/**
 * Checkout status push class
 */
class CheckoutStatus implements CheckoutStatusInterface
{
    /**
     * @var int
     */
    private $orderId;

    /**
     * @var string
     */
    private $merchantReference;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $timeStamp;

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getMerchantReference()
    {
        return $this->merchantReference;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getTimestamp()
    {
        return $this->timeStamp;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setOrderId($value)
    {
        $this->orderId = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantReference($value)
    {
        $this->merchantReference = $value;

        return $this;
    }

    /**
     * Set customer checkout status.
     * May take one of the following values:
     * - "InProcess" - The order is created but the customer hasn't completed the purchase yet
     * - "OnHold" - The customer has completed the order, but it is pending until a manual assessment is made
     * - "Completed" - The order is confirmed and the payment is complete
     * - "Refused" - For some reason, the order was refused
     *
     * @param string $value
     * @return $this
     */
    public function setStatus($value)
    {
        $this->status = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setTimestamp($value)
    {
        $this->timeStamp = $value;

        return $this;
    }
}
