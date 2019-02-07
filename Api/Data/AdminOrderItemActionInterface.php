<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * Admin Order Item Action interface
 */
interface AdminOrderItemActionInterface extends QliroOrderItemInterface
{
    /**
     * @return string
     */
    public function getActionType();

    /**
     * @return int
     */
    public function getPaymentTransactionId();

    /**
     * @var string $value
     */
    public function setActionType($value);

    /**
     * @var int $value
     */
    public function setPaymentTransactionId($value);
}
