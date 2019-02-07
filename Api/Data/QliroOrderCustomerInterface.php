<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * QliroOne Order Customer Info interface
 *
 * @api
 */
interface QliroOrderCustomerInterface extends ContainerInterface
{
    /**
     * @return string
     */
    public function getPersonalNumber();

    /**
     * @return string
     */
    public function getJuridicalType();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return string
     */
    public function getMobileNumber();

    /**
     * @return \Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterface
     */
    public function getAddress();

    /**
     * @return bool
     */
    public function getLockCustomerInformation();

    /**
     * @return bool
     */
    public function getLockCustomerEmail();

    /**
     * @return bool
     */
    public function getLockCustomerPersonalNumber();

    /**
     * @return bool
     */
    public function getLockCustomerMobileNumber();

    /**
     * @return bool
     */
    public function getLockCustomerAddress();

    /**
     * @param string $value
     * @return $this
     */
    public function setPersonalNumber($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setJuridicalType($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setEmail($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMobileNumber($value);

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterface $value
     * @return $this
     */
    public function setAddress($value);

    /**
     * @param bool $value
     * @return $this
     */
    public function setLockCustomerInformation($value);

    /**
     * @param bool $value
     * @return $this
     */
    public function setLockCustomerEmail($value);

    /**
     * @param bool $value
     * @return $this
     */
    public function setLockCustomerPersonalNumber($value);

    /**
     * @param bool $value
     * @return $this
     */
    public function setLockCustomerMobileNumber($value);

    /**
     * @param bool $value
     * @return $this
     */
    public function setLockCustomerAddress($value);
}
