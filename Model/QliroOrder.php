<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model;

use Qliro\QliroOne\Api\Data\QliroOrderInterface;
use Qliro\QliroOne\Api\Data\CheckoutStatusInterface as CheckoutStatus;

/**
 * QliroOne order class
 */
class QliroOrder implements QliroOrderInterface
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
     * @var string
     */
    private $orderHtmlSnippet;

    /**
     * Get current customer checkout status.
     * May take one of the following values:
     * - "InProcess" - The order is created but the customer hasn't completed the purchase yet
     * - "OnHold" - The customer has completed the order, but it is pending until a manual assessment is made
     * - "Completed" - The order is confirmed and the payment is complete
     * - "Refused" - For some reason, the order was refused
     *
     * @var string
     */
    private $customerCheckoutStatus;

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
     * @var \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[]
     */
    private $orderItems;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderPaymentMethodInterface
     */
    private $paymentMethod;

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
     * @return QliroOrder
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
     * @return QliroOrder
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
     * @return QliroOrder
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
     * @return QliroOrder
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
     * @return QliroOrder
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
     * @return QliroOrder
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function getOrderHtmlSnippet()
    {
        return $this->orderHtmlSnippet;
    }

    /**
     * @param string $orderHtmlSnippet
     * @return QliroOrder
     */
    public function setOrderHtmlSnippet($orderHtmlSnippet)
    {
        $this->orderHtmlSnippet = $orderHtmlSnippet;

        return $this;
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function getCustomerCheckoutStatus()
    {
        return $this->customerCheckoutStatus;
    }

    /**
     * @param string $customerCheckoutStatus
     * @return QliroOrder
     */
    public function setCustomerCheckoutStatus($customerCheckoutStatus)
    {
        $this->customerCheckoutStatus = $customerCheckoutStatus;

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
     * @return QliroOrder
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
     * @return QliroOrder
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
     * @return QliroOrder
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
     * @return QliroOrder
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
     * @return QliroOrder
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
     * @return QliroOrder
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
     * @return QliroOrder
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Check if QliroOne order was already placed
     *
     * @return bool
     */
    public function isPlaced()
    {
        return !in_array(
            $this->getCustomerCheckoutStatus(),
            [
                CheckoutStatus::STATUS_IN_PROCESS,
                CheckoutStatus::STATUS_ONHOLD
            ]
        );
    }
}
