<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Admin;

use Qliro\QliroOne\Api\Data\AdminTransactionResponseInterface;

/**
 * Admin QliroOne Order class
 */
class TransactionResponse implements AdminTransactionResponseInterface
{
    private $paymentTransactionId;
    private $status;
    private $type;
    private $reversalPaymentTransactionId;
    private $reversalPaymentTransactionStatus;

    /**
     * @return int
     */
    public function getPaymentTransactionId()
    {
        return $this->paymentTransactionId;
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
    public function getType()
    {
        $this->type;
    }

    /**
     * @return int
     */
    public function getReversalPaymentTransactionId()
    {
        $this->reversalPaymentTransactionId;
    }

    /**
     * @return string
     */
    public function getReversalPaymentTransactionStatus()
    {
        $this->reversalPaymentTransactionStatus;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setPaymentTransactionId($value)
    {
        $this->paymentTransactionId = $value;
        return $this;
    }

    /**
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
    public function setType($value)
    {
        // TODO: Implement setType() method.
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setReversalPaymentTransactionId($value)
    {
        // TODO: Implement setReversalPaymentTransactionId() method.
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setReversalPaymentTransactionStatus($value)
    {
        // TODO: Implement setReversalPaymentTransactionStatus() method.
    }
}
