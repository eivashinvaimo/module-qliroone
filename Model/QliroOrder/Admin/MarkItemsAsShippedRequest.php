<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Admin;

use Qliro\QliroOne\Api\Data\AdminMarkItemsAsShippedRequestInterface;

/**
 * Mark Items As Shipped Request class
 */
class MarkItemsAsShippedRequest implements AdminMarkItemsAsShippedRequestInterface
{
    /**
     * @var string
     */
    private $merchantApiKey;

    /**
     * @var int
     */
    private $orderId;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[]
     */
    private $orderItems;

    /**
     * @var string
     */
    private $requestId;

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
     * @return MarkItemsAsShippedRequest
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
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     * @return MarkItemsAsShippedRequest
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

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
     * @return MarkItemsAsShippedRequest
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
     * @return MarkItemsAsShippedRequest
     */
    public function setOrderItems($orderItems)
    {
        $this->orderItems = $orderItems;

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
     * @return MarkItemsAsShippedRequest
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;

        return $this;
    }
}
