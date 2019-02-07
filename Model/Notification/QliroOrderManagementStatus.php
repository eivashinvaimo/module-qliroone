<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Notification;

use Qliro\QliroOne\Api\Data\QliroOrderManagementStatusInterface;

/**
 * Management status push class
 */
class QliroOrderManagementStatus implements QliroOrderManagementStatusInterface
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
     * @var int
     */
    private $paymentTransactionId;

    /**
     * @var string
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $paymentType;

    /**
     * @var string
     */
    private $providerTransactionid;

    /**
     * @var string
     */
    private $providerResultCode;

    /**
     * @var string
     */
    private $providerResultDescription;

    /**
     * @var string
     */
    private $originalPaymentTransactionId;

    /**
     * @var int
     */
    private $paymentReference;

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
     * Can return one of the statuses declared in the interface
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getPaymentTransactionId()
    {
        return $this->paymentTransactionId;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * @return string
     */
    public function getProviderTransactionId()
    {
        return $this->providerTransactionid;
    }

    /**
     * @return string
     */
    public function getProviderResultCode()
    {
        return $this->providerResultCode;
    }

    /**
     * @return string
     */
    public function getProviderResultDescription()
    {
        return $this->providerResultDescription;
    }

    /**
     * @return string
     */
    public function getOriginalPaymentTransactionId()
    {
        return $this->originalPaymentTransactionId;
    }

    /**
     * @return int
     */
    public function getPaymentReference()
    {
        return $this->paymentReference;
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
     * Can only be set to one of the statuses declared in the interface
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
     * @param int $value
     * @return $this
     */
    public function setPaymentTransactionId($value)
    {
        $this->paymentTransactionId = $value;

        return $this;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function setAmount($value)
    {
        $this->amount = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setCurrency($value)
    {
        $this->currency = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPaymentType($value)
    {
        $this->paymentType = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setProviderTransactionId($value)
    {
        $this->providerTransactionid = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setProviderResultCode($value)
    {
        $this->providerResultCode = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setProviderResultDescription($value)
    {
        $this->providerResultDescription = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setOriginalPaymentTransactionId($value)
    {
        $this->originalPaymentTransactionId = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setPaymentReference($value)
    {
        $this->paymentReference = $value;

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
