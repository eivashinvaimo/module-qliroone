<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * Admin Order Payment Transaction interface
 */
interface AdminOrderPaymentTransactionInterface extends ContainerInterface
{
    /**
     * @return int
     */
    public function getPaymentTransactionId();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return string
     */
    public function getPaymentMethodName();

    /**
     * @param int $value
     * @return $this
     */
    public function setPaymentTransactionId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setType($value);

    /**
     * @param float $value
     * @return $this
     */
    public function setAmount($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCurrency($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setStatus($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setPaymentMethodName($value);
}
