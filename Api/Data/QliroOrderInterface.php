<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * Qliro Order interface.
 *
 * @api
 */
interface QliroOrderInterface extends ContainerInterface
{
    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @return string
     */
    public function getMerchantReference();

    /**
     * @return float
     */
    public function getTotalPrice();

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @return string
     */
    public function getLanguage();

    /**
     * @return string
     */
    public function getOrderHtmlSnippet();

    /**
     * Get current customer checkout status.
     *
     * May take one of the following values:
     * - "InProcess" - The order is created but the customer hasn't completed the purchase yet
     * - "OnHold" - The customer has completed the order, but it is pending until a manual assessment is made
     * - "Completed" - The order is confirmed and the payment is complete
     * - "Refused" - For some reason, the order was refused
     *
     * @return string
     */
    public function getCustomerCheckoutStatus();

    /**
     * @return bool
     */
    public function getSignupForNewsletter();

    /**
     * @return \Qliro\QliroOne\Api\Data\QliroOrderIdentityVerificationInterface
     */
    public function getIdentityVerification();

    /**
     * @return \Qliro\QliroOne\Api\Data\QliroOrderCustomerInterface
     */
    public function getCustomer();

    /**
     * @return \Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterface
     */
    public function getBillingAddress();

    /**
     * @return \Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterface
     */
    public function getShippingAddress();

    /**
     * @return \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[]
     */
    public function getOrderItems();

    /**
     * @return \Qliro\QliroOne\Api\Data\QliroOrderPaymentMethodInterface
     */
    public function getPaymentMethod();

    /**
     * @param int $value
     * @return $this
     */
    public function setOrderId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantReference($value);

    /**
     * @param float $value
     * @return $this
     */
    public function setTotalPrice($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCountry($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCurrency($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setLanguage($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setOrderHtmlSnippet($value);

    /**
     * Set customer checkout status.
     *
     * May take one of the following values:
     * - "InProcess" - The order is created but the customer hasn't completed the purchase yet
     * - "OnHold" - The customer has completed the order, but it is pending until a manual assessment is made
     * - "Completed" - The order is confirmed and the payment is complete
     * - "Refused" - For some reason, the order was refused
     *
     * @param string $value
     * @return $this
     */
    public function setCustomerCheckoutStatus($value);

    /**
     * @param bool $value
     * @return $this
     */
    public function setSignupForNewsletter($value);

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderIdentityVerificationInterface $value
     * @return $this
     */
    public function setIdentityVerification($value);

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderCustomerInterface $value
     * @return $this
     */
    public function setCustomer($value);

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterface $value
     * @return $this
     */
    public function setBillingAddress($value);

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterface $value
     * @return $this
     */
    public function setShippingAddress($value);

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[] $value
     * @return $this
     */
    public function setOrderItems($value);

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderPaymentMethodInterface $value
     * @return $this
     */
    public function setPaymentMethod($value);

    /**
     * Check if QliroOne order was already placed
     *
     * @return bool
     */
    public function isPlaced();
}
