<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Notification;

use Qliro\QliroOne\Api\Data\UpdateShippingMethodsNotificationInterface;

/**
 * Update Shipping Methods notification data class
 */
class UpdateShippingMethods implements UpdateShippingMethodsNotificationInterface
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
    private $juridicalType;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderCustomerInterface
     */
    private $customer;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterface
     */
    private $shippingAddress;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[]
     */
    private $orderItems;

    /**
     * @var string
     */
    private $selectedShippingMethod;

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
     * @return UpdateShippingMethods
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
    public function getMerchantReference()
    {
        return $this->merchantReference;
    }

    /**
     * @param string $merchantReference
     * @return UpdateShippingMethods
     */
    public function setMerchantReference($merchantReference)
    {
        $this->merchantReference = $merchantReference;

        return $this;
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function getJuridicalType()
    {
        return $this->juridicalType;
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param string $code
     * @return UpdateShippingMethods
     */
    public function setCountryCode($code)
    {
        $this->countryCode = $code;

        return $this;
    }

    /**
     * @param string $juridicalType
     * @return UpdateShippingMethods
     */
    public function setJuridicalType($juridicalType)
    {
        $this->juridicalType = $juridicalType;

        return $this;
    }

    /**
     * Getter.
     *
     * @return \Qliro\QliroOne\Api\Data\QliroOrderCustomerInterface
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderCustomerInterface $customer
     * @return UpdateShippingMethods
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Getter.
     *
     * @return \Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterface
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterface $shippingAddress
     * @return UpdateShippingMethods
     */
    public function setShippingAddress($shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;

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
     * @return UpdateShippingMethods
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
    public function getSelectedShippingMethod()
    {
        return $this->selectedShippingMethod;
    }

    /**
     * @param string $selectedShippingMethod
     * @return UpdateShippingMethods
     */
    public function setSelectedShippingMethod($selectedShippingMethod)
    {
        $this->selectedShippingMethod = $selectedShippingMethod;

        return $this;
    }
}
