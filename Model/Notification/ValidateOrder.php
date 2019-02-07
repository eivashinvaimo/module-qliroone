<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Notification;

use Qliro\QliroOne\Api\Data\ValidateOrderNotificationInterface;

/**
 * Validate Order notification class
 */
class ValidateOrder implements ValidateOrderNotificationInterface
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
    private $currency;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderIdentityVerificationInterface
     */
    private $identityVerification;

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
     * @var \Qliro\QliroOne\Api\Data\QliroOrderPaymentMethodInterface
     */
    private $paymentMethod;

    /**
     * @var string
     */
    private $selectedShippingMethod;

    /**
     * @var string
     */
    private $selectedShippingSecondaryOption;

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
     * @return $this
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
     * @return $this
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
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Getter.
     *
     * @return \Qliro\QliroOne\Api\Data\QliroOrderIdentityVerificationInterface
     */
    public function getIdentityVerification()
    {
        return $this->identityVerification;
    }

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderIdentityVerificationInterface $identityVerification
     * @return $this
     */
    public function setIdentityVerification($identityVerification)
    {
        $this->identityVerification = $identityVerification;

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
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function setOrderItems($orderItems)
    {
        $this->orderItems = $orderItems;

        return $this;
    }

    /**
     * Getter.
     *
     * @return \Qliro\QliroOne\Api\Data\QliroOrderPaymentMethodInterface
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderPaymentMethodInterface $paymentMethod
     * @return $this
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

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
     * @return $this
     */
    public function setSelectedShippingMethod($selectedShippingMethod)
    {
        $this->selectedShippingMethod = $selectedShippingMethod;

        return $this;
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function getSelectedShippingSecondaryOption()
    {
        return $this->selectedShippingSecondaryOption;
    }

    /**
     * @param string $selectedShippingSecondaryOption
     * @return $this
     */
    public function setSelectedShippingSecondaryOption($selectedShippingSecondaryOption)
    {
        $this->selectedShippingSecondaryOption = $selectedShippingSecondaryOption;

        return $this;
    }
}
