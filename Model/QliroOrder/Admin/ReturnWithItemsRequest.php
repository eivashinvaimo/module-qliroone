<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Admin;

use Qliro\QliroOne\Api\Data\AdminReturnWithItemsRequestInterface;

/**
 * Return With Items Request class
 */
class ReturnWithItemsRequest implements AdminReturnWithItemsRequestInterface
{
    /**
     * @var string
     */
    private $merchantApiKey;

    /**
     * @var int
     */
    private $paymentReference;

    /**
     * @var string
     */
    private $requestId;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[]
     */
    private $orderItems;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[]
     */
    private $fees;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[]
     */
    private $discounts;

    /**
     * Getter.
     *
     * @return string
     */
    public function getMerchantApiKey()
    {
        return $this->merchantApiKey;
    }

    /**
     * @param string $merchantApiKey
     * @return ReturnWithItemsRequest
     */
    public function setMerchantApiKey($merchantApiKey)
    {
        $this->merchantApiKey = $merchantApiKey;

        return $this;
    }

    /**
     * Getter.
     *
     * @return int
     */
    public function getPaymentReference()
    {
        return $this->paymentReference;
    }

    /**
     * @param int $paymentReference
     * @return ReturnWithItemsRequest
     */
    public function setPaymentReference($paymentReference)
    {
        $this->paymentReference = $paymentReference;

        return $this;
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @param string $requestId
     * @return ReturnWithItemsRequest
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;

        return $this;
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return ReturnWithItemsRequest
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Getter.
     *
     * @return \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[]
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[] $orderItems
     * @return ReturnWithItemsRequest
     */
    public function setOrderItems($orderItems)
    {
        $this->orderItems = $orderItems;

        return $this;
    }

    /**
     * Getter.
     *
     * @return \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[]
     */
    public function getFees()
    {
        return $this->fees;
    }

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[] $fees
     * @return ReturnWithItemsRequest
     */
    public function setFees($fees)
    {
        $this->fees = $fees;

        return $this;
    }

    /**
     * Getter.
     *
     * @return \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[]
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[] $discounts
     * @return ReturnWithItemsRequest
     */
    public function setDiscounts($discounts)
    {
        $this->discounts = $discounts;

        return $this;
    }
}
