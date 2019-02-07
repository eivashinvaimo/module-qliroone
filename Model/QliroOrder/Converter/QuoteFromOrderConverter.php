<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Converter;

use Magento\Quote\Model\Quote;
use Qliro\QliroOne\Api\SubscriptionInterface;

/**
 * Quote from QliroOne order container converter class
 */
class QuoteFromOrderConverter
{
    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Converter\OrderItemsConverter
     */
    private $orderItemsConverter;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Converter\CustomerConverter
     */
    private $customerConverter;

    /**
     * @var \Qliro\QliroOne\Model\Newsletter\SubscriptionInterface
     */
    private $subscription;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Converter\AddressConverter
     */
    private $addressConverter;

    /**
     * Inject dependnecies
     *
     * @param \Qliro\QliroOne\Model\QliroOrder\Converter\CustomerConverter $customerConverter
     * @param \Qliro\QliroOne\Model\QliroOrder\Converter\AddressConverter $addressConverter
     * @param \Qliro\QliroOne\Model\QliroOrder\Converter\OrderItemsConverter $orderItemsConverter
     * @param \Qliro\QliroOne\Api\SubscriptionInterface $subscription
     */
    public function __construct(
        CustomerConverter $customerConverter,
        AddressConverter $addressConverter,
        OrderItemsConverter $orderItemsConverter,
        SubscriptionInterface $subscription
    ) {
        $this->orderItemsConverter = $orderItemsConverter;
        $this->customerConverter = $customerConverter;
        $this->subscription = $subscription;
        $this->addressConverter = $addressConverter;
    }

    /**
     * Convert update shipping methods request into quote
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderInterface $container
     * @param \Magento\Quote\Model\Quote $quote
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function convert($container, Quote $quote)
    {
        $this->customerConverter->convert($container->getCustomer(), $quote);

        if ($qliroBillingAddress = $container->getBillingAddress()) {
            $this->addressConverter->convert(
                $qliroBillingAddress,
                $container->getCustomer(),
                $quote->getBillingAddress()
            );
        }

        if (!$quote->isVirtual() && ($qliroShippingAddress = $container->getShippingAddress())) {
            $this->addressConverter->convert(
                $qliroShippingAddress,
                $container->getCustomer(),
                $quote->getShippingAddress()
            );
        }

        $this->orderItemsConverter->convert($container->getOrderItems(), $quote);

        $signupForNewsletter = $container->getSignupForNewsletter();
        if ($signupForNewsletter) {
            $email = $quote->getCustomer()->getEmail();
            $this->subscription->addSubscription($email, $quote->getStoreId());
        }
    }
}
