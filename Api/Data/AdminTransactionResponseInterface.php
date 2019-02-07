<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * Admin Universalrsal Transaction Response interface
 */
interface AdminTransactionResponseInterface extends ContainerInterface
{
    const TYPE_UPDATE = 'UpdateItemsResponse';
    const TYPE_UPDATE_WITH_REVERSAL = 'UpdateItemsWithReversalResponse';

    /**
     * @return int
     */
    public function getPaymentTransactionId();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return int
     */
    public function getReversalPaymentTransactionId();

    /**
     * @return string
     */
    public function getReversalPaymentTransactionStatus();

    /**
     * @param int $value
     * @return $this
     */
    public function setPaymentTransactionId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setStatus($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setType($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setReversalPaymentTransactionId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setReversalPaymentTransactionStatus($value);
}
