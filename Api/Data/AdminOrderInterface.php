<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * Admin Order interface.
 *
 * @api
 */
interface AdminOrderInterface extends ContainerInterface
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
     * @return \Qliro\QliroOne\Api\Data\AdminOrderItemActionInterface[]
     */
    public function getOrderItemActions();

    /**
     * @return \Qliro\QliroOne\Api\Data\AdminOrderPaymentTransactionInterface[]
     */
    public function getPaymentTransactions();

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
     * @param \Qliro\QliroOne\Api\Data\AdminOrderItemActionInterface[] $value
     * @return $this
     */
    public function setOrderItemActions($value);

    /**
     * @param \Qliro\QliroOne\Api\Data\AdminOrderPaymentTransactionInterface[] $value
     * @return $this
     */
    public function setPaymentTransactions($value);
}
