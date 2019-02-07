<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Builder;

use Magento\Framework\Event\ManagerInterface;
use Magento\Quote\Model\Quote;
use Qliro\QliroOne\Api\Data\UpdateShippingMethodsResponseInterface;
use Qliro\QliroOne\Api\Data\UpdateShippingMethodsResponseInterfaceFactory;

/**
 * Shipping Methods Builder class
 */
class ShippingMethodsBuilder
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var \Qliro\QliroOne\Api\Data\UpdateShippingMethodsResponseInterfaceFactory
     */
    private $shippingMethodsResponseFactory;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Builder\ShippingMethodBuilder
     */
    private $shippingMethodBuilder;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Api\Data\UpdateShippingMethodsResponseInterfaceFactory $shippingMethodsResponseFactory
     * @param \Qliro\QliroOne\Model\QliroOrder\Builder\ShippingMethodBuilder $shippingMethodBuilder
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        UpdateShippingMethodsResponseInterfaceFactory $shippingMethodsResponseFactory,
        ShippingMethodBuilder $shippingMethodBuilder,
        ManagerInterface $eventManager
    ) {
        $this->shippingMethodsResponseFactory = $shippingMethodsResponseFactory;
        $this->shippingMethodBuilder = $shippingMethodBuilder;
        $this->eventManager = $eventManager;
    }

    /**
     * Set quote for data extraction
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function setQuote(Quote $quote)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * @return \Qliro\QliroOne\Api\Data\UpdateShippingMethodsResponseInterface
     */
    public function create()
    {
        if (empty($this->quote)) {
            throw new \LogicException('Quote entity is not set.');
        }

        /** @var \Qliro\QliroOne\Api\Data\UpdateShippingMethodsResponseInterface $container */
        $container = $this->shippingMethodsResponseFactory->create();

        $shippingAddress = $this->quote->getShippingAddress();
        $rateGroups = $shippingAddress->getGroupedAllShippingRates();

        $collectedShippingMethods = [];
        
        if ($this->quote->getIsVirtual()) {
            $container->setAvailableShippingMethods($collectedShippingMethods);
        } else {
            foreach ($rateGroups as $group) {
                /** @var \Magento\Quote\Model\Quote\Address\Rate $rate */
                foreach ($group as $rate) {
                    if (substr($rate->getCode(), -6) === '_error') {
                        continue;
                    }

                    $this->shippingMethodBuilder->setQuote($this->quote);
                    $this->shippingMethodBuilder->setShippingRate($rate);
                    $shippingMethodContainer = $this->shippingMethodBuilder->create();

                    if (!$shippingMethodContainer->getMerchantReference()) {
                        continue;
                    }

                    $collectedShippingMethods[] = $shippingMethodContainer;
                }
            }

            if (empty($collectedShippingMethods)) {
                $container->setDeclineReason(UpdateShippingMethodsResponseInterface::REASON_POSTAL_CODE);
            } else {
                $container->setAvailableShippingMethods($collectedShippingMethods);
            }
        }

        $this->eventManager->dispatch(
            'qliroone_shipping_methods_response_build_after',
            [
                'quote' => $this->quote,
                'container' => $container,
            ]
        );

        $this->quote = null;
        
        return $container;
    }
}
