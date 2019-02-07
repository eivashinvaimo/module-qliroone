<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Converter;

use Magento\Quote\Model\Quote;
use Qliro\QliroOne\Helper\Data as Helper;

/**
 * QliroOne Order customer Converter class
 */
class CustomerConverter
{
    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Converter\AddressConverter
     */
    private $addressConverter;

    /**
     * @var \Qliro\QliroOne\Helper\Data
     */
    private $helper;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Model\QliroOrder\Converter\AddressConverter $addressConverter
     * @param \Qliro\QliroOne\Helper\Data $helper
     */
    public function __construct(
        AddressConverter $addressConverter,
        Helper $helper
    ) {
        $this->addressConverter = $addressConverter;
        $this->helper = $helper;
    }

    /**
     * Convert QliroOne order customer info into quote customer
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderCustomerInterface $qliroCustomer
     * @param \Magento\Quote\Model\Quote $quote
     */
    public function convert($qliroCustomer, Quote $quote)
    {
        if ($qliroCustomer) {
            $customer = $quote->getCustomer();
            $qliroAddress = $qliroCustomer->getAddress();

            $customerData = [
                'taxvat' => $qliroCustomer->getPersonalNumber(),
                'juridical_type' => $qliroCustomer->getJuridicalType(),
                'email' => $qliroCustomer->getEmail(),
            ];

            foreach ($customerData as $key => $value) {
                if ($value !== null) {
                    $customer->setData($key, $value);
                }
            }

            if ($qliroAddress) {
                $billingAddress = $quote->getBillingAddress();
                $this->addressConverter->convert($qliroAddress, $qliroCustomer, $billingAddress);

                if (!$quote->isVirtual()) {
                    $shippingAddress = $quote->getShippingAddress();
                    $this->addressConverter->convert($qliroAddress, $qliroCustomer, $shippingAddress);
                    $shippingAddress->setSameAsBilling($this->helper->doAddressesMatch($shippingAddress, $billingAddress));
                }
            }
        }
    }
}
