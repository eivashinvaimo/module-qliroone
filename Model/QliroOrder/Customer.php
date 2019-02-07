<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder;

use Qliro\QliroOne\Api\Data\QliroOrderCustomerInterface;

/**
 * QliroOne order customer class
 */
class Customer implements QliroOrderCustomerInterface
{
    /**
     * @var string
     */
    private $personalNumber;

    /**
     * @var string
     */
    private $juridicalType;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $mobileNumber;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterface
     */
    private $address;

    /**
     * @var bool
     */
    private $lockCustomerInformation;

    /**
     * @var bool
     */
    private $lockCustomerEmail;

    /**
     * @var bool
     */
    private $lockCustomerPersonalNumber;

    /**
     * @var bool
     */
    private $lockCustomerMobileNumber;

    /**
     * @var bool
     */
    private $lockCustomerAddress;

    /**
     * Getter.
     *
     * @return string
     */
    public function getPersonalNumber()
    {
        return $this->personalNumber;
    }

    /**
     * @param string $personalNumber
     * @return Customer
     */
    public function setPersonalNumber($personalNumber)
    {
        $this->personalNumber = $personalNumber;

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
     * @param string $juridicalType
     * @return Customer
     */
    public function setJuridicalType($juridicalType)
    {
        $this->juridicalType = $juridicalType;

        return $this;
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    /**
     * @param string $mobileNumber
     * @return Customer
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;

        return $this;
    }

    /**
     * Getter.
     *
     * @return \Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterface
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterface $address
     * @return Customer
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Getter.
     *
     * @return bool
     */
    public function getLockCustomerInformation()
    {
        return $this->lockCustomerInformation;
    }

    /**
     * @param bool $lockCustomerInformation
     * @return Customer
     */
    public function setLockCustomerInformation($lockCustomerInformation)
    {
        $this->lockCustomerInformation = $lockCustomerInformation;

        return $this;
    }

    /**
     * Getter.
     *
     * @return bool
     */
    public function getLockCustomerEmail()
    {
        return $this->lockCustomerEmail;
    }

    /**
     * @param bool $lockCustomerEmail
     * @return Customer
     */
    public function setLockCustomerEmail($lockCustomerEmail)
    {
        $this->lockCustomerEmail = $lockCustomerEmail;

        return $this;
    }

    /**
     * Getter.
     *
     * @return bool
     */
    public function getLockCustomerPersonalNumber()
    {
        return $this->lockCustomerPersonalNumber;
    }

    /**
     * @param bool $lockCustomerPersonalNumber
     * @return Customer
     */
    public function setLockCustomerPersonalNumber($lockCustomerPersonalNumber)
    {
        $this->lockCustomerPersonalNumber = $lockCustomerPersonalNumber;

        return $this;
    }

    /**
     * Getter.
     *
     * @return bool
     */
    public function getLockCustomerMobileNumber()
    {
        return $this->lockCustomerMobileNumber;
    }

    /**
     * @param bool $lockCustomerMobileNumber
     * @return Customer
     */
    public function setLockCustomerMobileNumber($lockCustomerMobileNumber)
    {
        $this->lockCustomerMobileNumber = $lockCustomerMobileNumber;

        return $this;
    }

    /**
     * Getter.
     *
     * @return bool
     */
    public function getLockCustomerAddress()
    {
        return $this->lockCustomerAddress;
    }

    /**
     * @param bool $lockCustomerAddress
     * @return Customer
     */
    public function setLockCustomerAddress($lockCustomerAddress)
    {
        $this->lockCustomerAddress = $lockCustomerAddress;

        return $this;
    }
}
