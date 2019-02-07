<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Builder;

use Magento\Framework\Event\ManagerInterface;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Tax\Model\Calculation as TaxCalculation;
use Qliro\QliroOne\Api\Builder\OrderItemHandlerInterface;
use Qliro\QliroOne\Api\Data\QliroOrderItemInterfaceFactory;
use Qliro\QliroOne\Helper\Data as QliroHelper;
use Qliro\QliroOne\Model\Product\Type\QuoteSourceProvider;
use Qliro\QliroOne\Model\Product\Type\TypePoolHandler;

/**
 * QliroOne Order items builder class
 */
class OrderItemsBuilder
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    private $taxHelper;

    /**
     * @var \Qliro\QliroOne\Model\Product\Type\TypePoolHandler
     */
    private $typeResolver;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderItemInterfaceFactory
     */
    private $qliroOrderItemFactory;

    /**
     * @var \Magento\Tax\Model\Calculation
     */
    private $taxCalculation;

    /**
     * @var \Qliro\QliroOne\Helper\Data
     */
    private $qliroHelper;

    /**
     * @var \Qliro\QliroOne\Api\Builder\OrderItemHandlerInterface[]
     */
    private $handlers = [];

    /**
     * @var \Qliro\QliroOne\Model\Product\Type\QuoteSourceProvider
     */
    private $quoteSourceProvider;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * Inject dependencies
     *
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Qliro\QliroOne\Model\Product\Type\TypePoolHandler $typeResolver
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterfaceFactory $qliroOrderItemFactory
     * @param \Qliro\QliroOne\Helper\Data $qliroHelper
     * @param \Qliro\QliroOne\Model\Product\Type\QuoteSourceProvider $quoteSourceProvider
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Qliro\QliroOne\Api\Builder\OrderItemHandlerInterface[] $handlers
     */
    public function __construct(
        TaxHelper $taxHelper,
        TaxCalculation $taxCalculation,
        TypePoolHandler $typeResolver,
        QliroOrderItemInterfaceFactory $qliroOrderItemFactory,
        QliroHelper $qliroHelper,
        QuoteSourceProvider $quoteSourceProvider,
        ManagerInterface $eventManager,
        $handlers = []
    ) {
        $this->taxHelper = $taxHelper;
        $this->typeResolver = $typeResolver;
        $this->qliroOrderItemFactory = $qliroOrderItemFactory;
        $this->taxCalculation = $taxCalculation;
        $this->qliroHelper = $qliroHelper;
        $this->quoteSourceProvider = $quoteSourceProvider;
        $this->eventManager = $eventManager;
        $this->handlers = $handlers;
    }

    /**
     * Set quote for data extraction
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return $this
     */
    public function setQuote(CartInterface $quote)
    {
        $this->quote = $quote;
        $this->quoteSourceProvider->setQuote($this->quote);

        return $this;
    }

    /**
     * Create an array of containers
     *
     * @return \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[]
     */
    public function create()
    {
        if (empty($this->quote)) {
            throw new \LogicException('Quote entity is not set.');
        }

        $result = [];

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($this->quote->getAllItems() as $item) {
            $qliroOrderItem = $this->typeResolver->resolveQliroOrderItem(
                $this->quoteSourceProvider->generateSourceItem($item),
                $this->quoteSourceProvider
            );

            if ($qliroOrderItem) {
                $this->eventManager->dispatch(
                    'qliroone_order_item_build_after',
                    [
                        'quote' => $this->quote,
                        'container' => $qliroOrderItem,
                    ]
                );

                if ($qliroOrderItem->getMerchantReference()) {
                    $result[] = $qliroOrderItem;
                }
            }
        }

        foreach ($this->handlers as $handler) {
            if ($handler instanceof OrderItemHandlerInterface) {
                $result = $handler->handle($result, $this->quote);
            }
        }

        $this->quote = null;
        $this->quoteSourceProvider->setQuote($this->quote);

        return $result;
    }
}
