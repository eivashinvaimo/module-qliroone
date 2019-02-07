<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Product\Type;

use Magento\Quote\Model\Quote;
use Qliro\QliroOne\Api\Product\TypeSourceItemInterface;
use Qliro\QliroOne\Api\Product\TypeSourceItemInterfaceFactory;
use Qliro\QliroOne\Api\Product\TypeSourceProviderInterface;
use Qliro\QliroOne\Model\Product\ProductPool;

/**
 * Quote Source Provider class
 */
class QuoteSourceProvider implements TypeSourceProviderInterface
{
    /**
     * @var array
     */
    private $sourceItems = [];

    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var \Qliro\QliroOne\Model\Product\ProductPool
     */
    private $productPool;

    /**
     * @var \Qliro\QliroOne\Api\Product\TypeSourceItemInterfaceFactory
     */
    private $typeSourceItemFactory;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Model\Product\ProductPool $productPool
     * @param \Qliro\QliroOne\Api\Product\TypeSourceItemInterfaceFactory $typeSourceItemFactory
     */
    public function __construct(
        ProductPool $productPool,
        TypeSourceItemInterfaceFactory $typeSourceItemFactory
    ) {
        $this->productPool = $productPool;
        $this->typeSourceItemFactory = $typeSourceItemFactory;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->getStoreId();
    }

    /**
     * @param string $reference
     * @return \Qliro\QliroOne\Api\Product\TypeSourceItemInterface
     */
    public function getSourceItemByMerchantReference($reference)
    {
        if (strpos($reference, ':') !== false) {
            list($quoteItemId, $sku) = explode(':', $reference);
        } else {
            $quoteItemId = null;
            $sku = $reference;
        }

        try {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quoteItem = $this->quote->getItemById($quoteItemId);

            if (!$quoteItem) {
                if ($sku) {
                    $product = $this->productPool->getProduct($sku, $this->getStoreId());

                    /** @var \Magento\Quote\Model\Quote $quote */
                    $quoteItem = $quote->getItemByProduct($product);
                } else {
                    $quoteItem = null;
                }
            }

            if ($quoteItem) {
                // Basically, at this point we do not update quote items

                return $this->generateSourceItem($quoteItem);
            }

            return null;
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @return \Qliro\QliroOne\Api\Product\TypeSourceItemInterface[]
     */
    public function getSourceItems()
    {
        $result = [];

        foreach ($this->quote->getAllVisibleItems() as $item) {
            $result[] = $this->generateSourceItem($item);
        }

        return $result;
    }

    /**
     * Set quote
     *
     * @param \Magento\Quote\Model\Quote $quote
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return \Qliro\QliroOne\Api\Product\TypeSourceItemInterface
     */
    public function generateSourceItem($item)
    {
        if (!isset($this->sourceItems[$item->getItemId()])) {
            /** @var \Qliro\QliroOne\Api\Product\TypeSourceItemInterface $sourceItem */
            $sourceItem = $this->typeSourceItemFactory->create();

            $sourceItem->setId($item->getItemId());
            $sourceItem->setName($item->getName());
            $sourceItem->setPriceInclTax($item->getPriceInclTax());
            $sourceItem->setPriceExclTax($item->getPrice());
            $sourceItem->setQty($item->getQty());
            $sourceItem->setSku($item->getSku());
            $sourceItem->setType($item->getProductType());
            $sourceItem->setProduct($item->getProduct());
            $sourceItem->setItem($item);

            $this->sourceItems[$item->getItemId()] = $sourceItem;

            if ($parentItem = $item->getParentItem()) {
                $sourceItem->setParent($this->generateSourceItem($parentItem));
            }
        }

        return $this->sourceItems[$item->getItemId()];
    }
}
