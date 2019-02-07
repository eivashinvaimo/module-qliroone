<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * Checkout Status Notification interface
 *
 * @api
 */
interface CheckoutStatusInterface extends ContainerInterface
{
    const STATUS_IN_PROCESS = 'InProcess';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_ONHOLD = 'OnHold';
    const STATUS_REFUSED = 'Refused';

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @return string
     */
    public function getMerchantReference();

    /**
     * Get current customer checkout status.
     *
     * May take one of the following values:
     * - "InProcess" - The order is created but the customer hasn't completed the purchase yet
     * - "OnHold" - The customer has completed the order, but it is pending until a manual assessment is made
     * - "Completed" - The order is confirmed and the payment is complete
     * - "Refused" - For some reason, the order was refused
     *
     * @return string
     */
    public function getStatus();

    /**
     * @return string
     */
    public function getTimestamp();

    /**
     * @param int $value
     * @return $this
     */
    public function setOrderId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantReference($value);

    /**
     * Set customer checkout status.
     *
     * May take one of the following values:
     * - "InProcess" - The order is created but the customer hasn't completed the purchase yet
     * - "OnHold" - The customer has completed the order, but it is pending until a manual assessment is made
     * - "Completed" - The order is confirmed and the payment is complete
     * - "Refused" - For some reason, the order was refused
     *
     * @param string $value
     * @return $this
     */
    public function setStatus($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setTimestamp($value);
}
