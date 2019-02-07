<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Builder;

use Magento\Customer\Model\Address;
use Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterfaceFactory;

/**
 * QliroOne Order Customer Address builder class
 */
class CustomerAddressBuilder
{
    const STREET_ADDRESS_SEPARATOR = '; ';

    /**
     * @var \Magento\Customer\Model\Address
     */
    private $address;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderCustomerInterfaceFactory
     */
    private $orderCustomerAddressFactory;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterfaceFactory $orderCustomerAddressFactory
     */
    public function __construct(QliroOrderCustomerAddressInterfaceFactory $orderCustomerAddressFactory)
    {
        $this->orderCustomerAddressFactory = $orderCustomerAddressFactory;
    }

    /**
     * Set a customer to extract data
     *
     * @param \Magento\Customer\Model\Address $address
     * @return $this
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Create a container
     *
     * @return \Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterface
     */
    public function create()
    {
        if (empty($this->address)) {
            throw new \LogicException('Customer address entity is not set.');
        }

        /** @var \Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterface $qliroOrderCustomerAddress */
        $qliroOrderCustomerAddress = $this->orderCustomerAddressFactory->create();

        $streetAddress = trim(implode(self::STREET_ADDRESS_SEPARATOR, $this->address->getStreet()));

        $qliroOrderCustomerAddress->setFirstName($this->address->getFirstname());
        $qliroOrderCustomerAddress->setLastName($this->address->getLastname());
        $qliroOrderCustomerAddress->setCompanyName($this->address->getCompany());
        $qliroOrderCustomerAddress->setStreet($streetAddress);
        $qliroOrderCustomerAddress->setPostalCode($this->address->getPostcode());
        $qliroOrderCustomerAddress->setCity($this->address->getCity());

        $this->address = null;

        return $qliroOrderCustomerAddress;
    }

}
