<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Builder;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AddressFactory;
use Qliro\QliroOne\Api\Data\QliroOrderCustomerInterfaceFactory;

/**
 * QliroOne Order Customer builder class
 */
class CustomerBuilder
{
    /**
     * @var \Magento\Customer\Api\Data\CustomerInterface
     */
    private $customer;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderCustomerInterfaceFactory
     */
    private $orderCustomerFactory;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Builder\CustomerAddressBuilder
     */
    private $customerAddressBuilder;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    private $addressFactory;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderCustomerInterfaceFactory $orderCustomerFactory
     * @param \Qliro\QliroOne\Model\QliroOrder\Builder\CustomerAddressBuilder $customerAddressBuilder
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     */
    public function __construct(
        QliroOrderCustomerInterfaceFactory $orderCustomerFactory,
        CustomerAddressBuilder $customerAddressBuilder,
        AddressFactory $addressFactory
    ) {
        $this->orderCustomerFactory = $orderCustomerFactory;
        $this->customerAddressBuilder = $customerAddressBuilder;
        $this->addressFactory = $addressFactory;
    }

    /**
     * Set a customer to extract data
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return $this
     */
    public function setCustomer(CustomerInterface $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Create a container
     *
     * @return \Qliro\QliroOne\Api\Data\QliroOrderCustomerInterface
     */
    public function create()
    {
        if (empty($this->customer)) {
            throw new \LogicException('Customer entity is not set.');
        }

        /** @var \Qliro\QliroOne\Api\Data\QliroOrderCustomerInterface $qliroOrderCustomer */
        $qliroOrderCustomer = $this->orderCustomerFactory->create();

        $addressId = $this->customer->getDefaultBilling();


        $address = $this->addressFactory->create()->load($addressId);

        $qliroOrderCustomerAddress = $this->customerAddressBuilder->setAddress($address)->create();

        $qliroOrderCustomer->setPersonalNumber($this->customer->getTaxvat());
        $qliroOrderCustomer->setJuridicalType(null); // TODO: Potentially we need to create a customer attribute
        $qliroOrderCustomer->setEmail($this->customer->getEmail());
        $qliroOrderCustomer->setMobileNumber(null);
        $qliroOrderCustomer->setAddress($qliroOrderCustomerAddress);
        $qliroOrderCustomer->setLockCustomerInformation(false);
        $qliroOrderCustomer->setLockCustomerEmail(false);
        $qliroOrderCustomer->setLockCustomerPersonalNumber(false);
        $qliroOrderCustomer->setLockCustomerMobileNumber(false);
        $qliroOrderCustomer->setLockCustomerAddress(false);

        $this->customer = null;

        return $qliroOrderCustomer;
    }
}
