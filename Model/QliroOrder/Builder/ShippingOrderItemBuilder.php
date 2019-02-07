<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Builder;

use Magento\Framework\Event\ManagerInterface;
use Magento\Quote\Model\Quote;
use Magento\Tax\Helper\Data as TaxHelper;
use Qliro\QliroOne\Api\Data\QliroOrderItemInterfaceFactory;

/**
 * QliroOne Order Item of type "Shipping" builder class
 */
class ShippingOrderItemBuilder
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderItemInterfaceFactory
     */
    private $orderItemFactory;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    private $taxHelper;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterfaceFactory $orderItemFactory
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        QliroOrderItemInterfaceFactory $orderItemFactory,
        TaxHelper $taxHelper,
        ManagerInterface $eventManager
    ) {
        $this->orderItemFactory = $orderItemFactory;
        $this->taxHelper = $taxHelper;
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
     * Create a QliroOne order item container for a shipping method
     *
     * @return \Qliro\QliroOne\Api\Data\QliroOrderItemInterface
     */
    public function create()
    {
        if (empty($this->quote)) {
            throw new \LogicException('Quote entity is not set.');
        }

        $shippingAddress = $this->quote->getShippingAddress();
        $code = $shippingAddress->getShippingMethod();
        $rate = $shippingAddress->getShippingRateByCode($code);

        /** @var \Qliro\QliroOne\Api\Data\QliroOrderItemInterface $container */
        $container = $this->orderItemFactory->create();

        $priceExVat = $this->taxHelper->getShippingPrice(
            $rate->getPrice(),
            false,
            $shippingAddress,
            $this->quote->getCustomerTaxClassId()
        );

        $priceIncVat = $this->taxHelper->getShippingPrice(
            $rate->getPrice(),
            true,
            $shippingAddress,
            $this->quote->getCustomerTaxClassId()
        );

        $container->setMerchantReference($code);

        $container->setPricePerItemIncVat($priceIncVat);
        $container->setPricePerItemExVat($priceExVat);

        $this->eventManager->dispatch(
            'qliroone_order_item_build_after',
            [
                'quote' => $this->quote,
                'container' => $container,
            ]
        );

        $this->quote = null;

        return $container;
    }
}
