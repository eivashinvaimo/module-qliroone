<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * ValidateOrderNotificationInterface interface
 *
 * @api
 */
interface ValidateOrderNotificationInterface extends ContainerInterface
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
     * @return string
     */
    public function getCurrency();

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
     * @return string
     */
    public function getSelectedShippingMethod();

    /**
     * @return string
     */
    public function getSelectedShippingSecondaryOption();

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
     * @param string $value
     * @return $this
     */
    public function setCurrency($value);

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
     * @param string $value
     * @return $this
     */
    public function setSelectedShippingMethod($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setSelectedShippingSecondaryOption($value);
}
