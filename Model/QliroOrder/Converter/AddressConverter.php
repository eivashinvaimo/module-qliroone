<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Converter;

use Magento\Quote\Model\Quote\Address;

/**
 * QliroOne order address converter class
 */
class AddressConverter
{
    /**
     * Convert given quote address from QliroOne address and other parameters
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderCustomerAddressInterface $qliroAddress
     * @param \Qliro\QliroOne\Api\Data\QliroOrderCustomerInterface $qliroCustomer
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param string|null $countryCode
     */
    public function convert(
        $qliroAddress,
        $qliroCustomer,
        Address $address,
        $countryCode = null
    ) {
        $addressData = [
            'firstname' => $qliroAddress ? $qliroAddress->getFirstName() : null,
            'lastname' => $qliroAddress ? $qliroAddress->getLastName() : null,
            'email' => $qliroCustomer? $qliroCustomer->getEmail() : null,
            'care_of' => $qliroAddress ? $qliroAddress->getCareOf() : null, // Is ignored for now if no attribute
            'street' => $qliroAddress ? $qliroAddress->getStreet() : null,
            'telephone' => $qliroCustomer ? $qliroCustomer->getMobileNumber() : null,
            'city' => $qliroAddress ? $qliroAddress->getCity() : null,
            'postcode' => $qliroAddress ? $qliroAddress->getPostalCode() : null,
            'company' => $qliroAddress ? $qliroAddress->getCompanyName() : null,
        ];

        foreach ($addressData as $key => $value) {
            if ($value !== null) {
                $address->setData($key, $value);
            }
        }

        if (!$address->getCountryId() && $countryCode !== null) {
            $address->setCountryId($countryCode);
        }
    }
}
