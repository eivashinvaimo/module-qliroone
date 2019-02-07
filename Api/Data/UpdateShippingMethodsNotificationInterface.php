<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * Update Shipping Method Notification interface
 *
 * @api
 */
interface UpdateShippingMethodsNotificationInterface extends ContainerInterface
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
    public function getJuridicalType();

    /**
     * Get CountryCode for identifying country.
     * Node that this field is NOT DOCUMENTED
     *
     * @return string
     */
    public function getCountryCode();

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
     * @return string
     */
    public function getSelectedShippingMethod();

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
    public function setJuridicalType($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCountryCode($value);

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
     * @param string $value
     * @return $this
     */
    public function setSelectedShippingMethod($value);
}
