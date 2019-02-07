<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Converter;

use Magento\Quote\Model\Quote;
use Qliro\QliroOne\Api\Data\ValidateOrderNotificationInterface;

/**
 * Quote from validate order container converter class
 */
class QuoteFromValidateConverter
{
    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Converter\AddressConverter
     */
    private $addressConverter;

    /**
     * Inject dependnecies
     *
     * @param \Qliro\QliroOne\Model\QliroOrder\Converter\AddressConverter $addressConverter
     */
    public function __construct(
        AddressConverter $addressConverter
    ) {
        $this->addressConverter = $addressConverter;
    }

    /**
     * Convert validate order request into quote
     *
     * @param \Qliro\QliroOne\Api\Data\ValidateOrderNotificationInterface $container
     * @param \Magento\Quote\Model\Quote $quote
     */
    public function convert(ValidateOrderNotificationInterface $container, Quote $quote)
    {
        if ($quote->isVirtual()) {
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setShippingMethod($container->getSelectedShippingMethod());
            $this->addressConverter->convert($container->getShippingAddress(), $container->getCustomer(), $shippingAddress);
        }
    }
}
