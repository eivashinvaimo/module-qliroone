<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Builder;

use Magento\Framework\Event\ManagerInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Tax\Helper\Data as TaxHelper;
use Qliro\QliroOne\Api\Data\QliroOrderShippingMethodInterfaceFactory;
use Qliro\QliroOne\Api\ShippingMethodBrandResolverInterface;
use Qliro\QliroOne\Helper\Data;

/**
 * QliroOne Order Item of type "Shipping" builder class
 */
class ShippingMethodBuilder
{
    /**
     * @var \Magento\Quote\Model\Quote\Address\Rate
     */
    private $rate;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    private $taxHelper;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderShippingMethodInterfaceFactory
     */
    private $shippingMethodFactory;

    /**
     * @var \Qliro\QliroOne\Api\ShippingMethodBrandResolverInterface
     */
    private $shippingMethodBrandResolver;

    /**
     * @var \Qliro\QliroOne\Helper\Data
     */
    private $qliroHelper;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderShippingMethodInterfaceFactory $shippingMethodFactory
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Qliro\QliroOne\Api\ShippingMethodBrandResolverInterface $shippingMethodBrandResolver
     * @param \Qliro\QliroOne\Helper\Data $qliroHelper
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        QliroOrderShippingMethodInterfaceFactory $shippingMethodFactory,
        TaxHelper $taxHelper,
        ShippingMethodBrandResolverInterface $shippingMethodBrandResolver,
        Data $qliroHelper,
        ManagerInterface $eventManager
    ) {
        $this->taxHelper = $taxHelper;
        $this->shippingMethodFactory = $shippingMethodFactory;
        $this->shippingMethodBrandResolver = $shippingMethodBrandResolver;
        $this->qliroHelper = $qliroHelper;
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
     * Set shipping rate for data extraction
     *
     * @param \Magento\Quote\Model\Quote\Address\Rate $rate
     * @return $this
     */
    public function setShippingRate(Rate $rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Create a QliroOne order shipping method container
     *
     * @return \Qliro\QliroOne\Api\Data\QliroOrderShippingMethodInterface
     */
    public function create()
    {
        if (empty($this->quote)) {
            throw new \LogicException('Quote entity is not set.');
        }

        if (empty($this->rate)) {
            throw new \LogicException('Shipping rate entity is not set.');
        }

        $shippingAddress = $this->quote->getShippingAddress();
        /** @var \Qliro\QliroOne\Api\Data\QliroOrderShippingMethodInterface $container */
        $container = $this->shippingMethodFactory->create();

        $priceExVat = $this->taxHelper->getShippingPrice(
            $this->rate->getPrice(),
            false,
            $shippingAddress,
            $this->quote->getCustomerTaxClassId()
        );

        $priceIncVat = $this->taxHelper->getShippingPrice(
            $this->rate->getPrice(),
            true,
            $shippingAddress,
            $this->quote->getCustomerTaxClassId()
        );

        $container->setMerchantReference($this->rate->getCode());
        $container->setDisplayName($this->rate->getMethodTitle());
        $container->setBrand($this->shippingMethodBrandResolver->resolve($this->rate));

        $descriptions = [];

        if ($this->rate->getCarrierTitle() !== null) {
            $descriptions[] = $this->rate->getCarrierTitle();
        }

        if ($this->rate->getMethodDescription() !== null) {
            $descriptions[] = $this->rate->getMethodDescription();
        }

        if (!empty($descriptions)) {
            $container->setDescriptions($descriptions);
        }

        $container->setPriceIncVat($this->qliroHelper->formatPrice($priceIncVat));
        $container->setPriceExVat($this->qliroHelper->formatPrice($priceExVat));
        
        $this->eventManager->dispatch(
            'qliroone_shipping_method_build_after',
            [
                'quote' => $this->quote,
                'rate' => $this->rate,
                'container' => $container,
            ]
        );

        $this->quote = null;
        $this->rate = null;

        return $container;
    }
}
