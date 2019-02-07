<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Admin;

use Qliro\QliroOne\Api\Data\AdminOrderInterface;

/**
 * Admin QliroOne Order class
 */
class AdminOrder implements AdminOrderInterface
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
     * @var float
     */
    private $totalPrice;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $language;

    /**
     * @var bool
     */
    private $signupForNewsletter;

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
    private $billingAddress;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterface
     */
    private $shippingAddress;

    /**
     * @var \Qliro\QliroOne\Api\Data\AdminOrderItemActionInterface[]
     */
    private $orderItemActions;

    /**
     * @var \Qliro\QliroOne\Api\Data\AdminOrderPaymentTransactionInterface[]
     */
    private $paymentTransactions;

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
     * @return AdminOrder
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
     * @return AdminOrder
     */
    public function setMerchantReference($merchantReference)
    {
        $this->merchantReference = $merchantReference;

        return $this;
    }

    /**
     * Getter.
     *
     * @return float
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @param float $totalPrice
     * @return AdminOrder
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return AdminOrder
     */
    public function setCountry($country)
    {
        $this->country = $country;

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
     * @return AdminOrder
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return AdminOrder
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Getter.
     *
     * @return bool
     */
    public function getSignupForNewsletter()
    {
        return $this->signupForNewsletter;
    }

    /**
     * @param bool $signupForNewsletter
     * @return AdminOrder
     */
    public function setSignupForNewsletter($signupForNewsletter)
    {
        $this->signupForNewsletter = $signupForNewsletter;

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
     * @return AdminOrder
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
     * @return AdminOrder
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
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterface $billingAddress
     * @return AdminOrder
     */
    public function setBillingAddress($billingAddress)
    {
        $this->billingAddress = $billingAddress;

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
     * @return AdminOrder
     */
    public function setShippingAddress($shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    /**
     * Getter.
     *
     * @return \Qliro\QliroOne\Api\Data\AdminOrderItemActionInterface[]
     */
    public function getOrderItemActions()
    {
        return $this->orderItemActions;
    }

    /**
     * @param \Qliro\QliroOne\Api\Data\AdminOrderItemActionInterface[] $orderItemActions
     * @return AdminOrder
     */
    public function setOrderItemActions($orderItemActions)
    {
        $this->orderItemActions = $orderItemActions;

        return $this;
    }

    /**
     * Getter.
     *
     * @return \Qliro\QliroOne\Api\Data\AdminOrderPaymentTransactionInterface[]
     */
    public function getPaymentTransactions()
    {
        return $this->paymentTransactions;
    }

    /**
     * @param \Qliro\QliroOne\Api\Data\AdminOrderPaymentTransactionInterface[] $paymentTransactions
     * @return AdminOrder
     */
    public function setPaymentTransactions($paymentTransactions)
    {
        $this->paymentTransactions = $paymentTransactions;

        return $this;
    }
}
