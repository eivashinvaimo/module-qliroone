<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Builder;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Qliro\QliroOne\Api\Data\QliroOrderItemInterface;
use Qliro\QliroOne\Api\Data\ValidateOrderResponseInterface;
use Qliro\QliroOne\Api\Data\ValidateOrderResponseInterfaceFactory;

/**
 * Shipping Methods Builder class
 */
class ValidateOrderBuilder
{
    /**
     * @var \Qliro\QliroOne\Api\Data\ValidateOrderNotificationInterface
     */
    private $validationRequest;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var \Qliro\QliroOne\Api\Data\UpdateShippingMethodsResponseInterfaceFactory
     */
    private $validateOrderResponseFactory;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Builder\OrderItemsBuilder
     */
    private $orderItemsBuilder;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Api\Data\ValidateOrderResponseInterfaceFactory $validateOrderResponseFactory
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Qliro\QliroOne\Model\QliroOrder\Builder\OrderItemsBuilder $orderItemsBuilder
     */
    public function __construct(
        ValidateOrderResponseInterfaceFactory $validateOrderResponseFactory,
        StockRegistryInterface $stockRegistry,
        OrderItemsBuilder $orderItemsBuilder
    ) {
        $this->validateOrderResponseFactory = $validateOrderResponseFactory;
        $this->stockRegistry = $stockRegistry;
        $this->orderItemsBuilder = $orderItemsBuilder;
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

        return $this;
    }

    /**
     * Set validation request for data extraction
     *
     * @param \Qliro\QliroOne\Api\Data\ValidateOrderNotificationInterface $validationRequest
     * @return $this
     */
    public function setValidationRequest($validationRequest)
    {
        $this->validationRequest = $validationRequest;

        return $this;
    }

    /**
     * @return \Qliro\QliroOne\Api\Data\ValidateOrderResponseInterface
     */
    public function create()
    {
        if (empty($this->quote)) {
            throw new \LogicException('Quote entity is not set.');
        }

        if (empty($this->validationRequest)) {
            throw new \LogicException('QliroOne validation request is not set.');
        }

        /** @var \Qliro\QliroOne\Api\Data\ValidateOrderResponseInterface $container */
        $container = $this->validateOrderResponseFactory->create();

        $allInStock = $this->checkItemsInStock();

        if (!$allInStock) {
            $this->quote = null;
            $this->validationRequest = null;

            return $container->setDeclineReason(ValidateOrderResponseInterface::REASON_OUT_OF_STOCK);
        }

        if (!$this->quote->isVirtual() && !$this->quote->getShippingAddress()->getShippingMethod()) {
            $this->quote = null;
            $this->validationRequest = null;

            return $container->setDeclineReason(ValidateOrderResponseInterface::REASON_SHIPPING);
        }

        $orderItemsFromQuote = $this->orderItemsBuilder->setQuote($this->quote)->create();

        $allMatch = $this->compareQuoteAndQliroOrderItems(
            $orderItemsFromQuote,
            $this->validationRequest->getOrderItems()
        );

        if (!$allMatch) {
            return $container->setDeclineReason(ValidateOrderResponseInterface::REASON_OTHER);
        }

        $container->setDeclineReason(null);

        $this->quote = null;
        $this->validationRequest = null;

        return $container;
    }

    /**
     * Check if any items are out of stock
     *
     * @return bool
     */
    private function checkItemsInStock()
    {
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        foreach ($this->quote->getAllVisibleItems() as $quoteItem) {
            $stockItem = $this->stockRegistry->getStockItem(
                $quoteItem->getProduct()->getId(),
                $quoteItem->getProduct()->getStore()->getWebsiteId()
            );

            if (!$stockItem->getIsInStock()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return true if the quote items and QliroOne order items match
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[] $quoteItems
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[] $qliroOrderItems
     * @return bool
     */
    private function compareQuoteAndQliroOrderItems($quoteItems, $qliroOrderItems)
    {
        $hashedQuoteItems = [];
        $hashedQliroItems = [];

        $skipTypes = [QliroOrderItemInterface::TYPE_SHIPPING, QliroOrderItemInterface::TYPE_FEE];

        // Gather order items converted from quote and hash them for faster search
        foreach ($quoteItems as $quoteItem) {
            if (!in_array($quoteItem->getType(), $skipTypes)) {
                $hashedQuoteItems[$quoteItem->getMerchantReference()] = $quoteItem;
            }
        }

        // Gather order items from QliroOne order and hash them for faster search, then try to see a diff
        foreach ($qliroOrderItems as $qliroOrderItem) {
            if (!in_array($qliroOrderItem->getType(), $skipTypes)) {
                $hash = $qliroOrderItem->getMerchantReference();
                $hashedQliroItems[$hash] = $qliroOrderItem;

                if (!isset($hashedQuoteItems[$hash])) {
                    return false;
                }

                if (!$this->compareItems($hashedQuoteItems[$hash], $hashedQliroItems[$hash])) {
                    return false;
                }
            }
        }

        // Try to see a diff between order items converted from quote and from QliroOne order
        foreach ($quoteItems as $quoteItem) {
            if (!in_array($quoteItem->getType(), $skipTypes)) {
                $hash = $quoteItem->getMerchantReference();

                if (!isset($hashedQliroItems[$hash])) {
                    return false;
                }

                if (!$this->compareItems($hashedQuoteItems[$hash], $hashedQliroItems[$hash])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Compare two QliroOne order items
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterface $item1
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterface $item2
     * @return bool
     */
    private function compareItems(QliroOrderItemInterface $item1, QliroOrderItemInterface $item2)
    {
        if ($item1->getPricePerItemExVat() != $item2->getPricePerItemExVat()) {
            return false;
        }

        if ($item1->getPricePerItemIncVat() != $item2->getPricePerItemIncVat()) {
            return false;
        }

        if ($item1->getQuantity() != $item2->getQuantity()) {
            return false;
        }

        if ($item1->getType() != $item2->getType()) {
            return false;
        }

        return true;
    }
}
