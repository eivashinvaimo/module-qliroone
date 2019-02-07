<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Product\Type;

use Magento\Sales\Model\Order;
use Qliro\QliroOne\Api\Product\TypeSourceItemInterface;
use Qliro\QliroOne\Api\Product\TypeSourceItemInterfaceFactory;
use Qliro\QliroOne\Api\Product\TypeSourceProviderInterface;
use Qliro\QliroOne\Model\Product\ProductPool;

/**
 * Order Source Provider class
 */
class OrderSourceProvider implements TypeSourceProviderInterface
{
    /**
     * @var array
     */
    private $sourceItems = [];

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $order;

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
            /** @var \Magento\Sales\Model\Order $order */
            $orderItem = $this->order->getItemByQuoteItemId($quoteItemId);

            if (!$orderItem) {
                if ($sku) {
                    $product = $this->productPool->getProduct($sku, $this->getStoreId());

                    /** @var \Magento\Sales\Model\Order $order */
                    $orderItem = $order->getItemById($product);
                } else {
                    $orderItem = null;
                }
            }

            if ($orderItem) {
                return $this->generateSourceItem($orderItem);
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

        foreach ($this->order->getAllVisibleItems() as $item) {
            $result[] = $this->generateSourceItem($item);
        }

        return $result;
    }

    /**
     * Set order
     *
     * @param \Magento\Sales\Model\Order $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $item
     * @return \Qliro\QliroOne\Api\Product\TypeSourceItemInterface
     */
    public function generateSourceItem($item)
    {
        if (!isset($this->sourceItems[$item->getQuoteItemId()])) {
            /** @var \Qliro\QliroOne\Api\Product\TypeSourceItemInterface $sourceItem */
            $sourceItem = $this->typeSourceItemFactory->create();

            $sourceItem->setId($item->getQuoteItemId());
            $sourceItem->setName($item->getName());
            $sourceItem->setPriceInclTax($item->getPriceInclTax());
            $sourceItem->setPriceExclTax($item->getPrice());
            $sourceItem->setQty($item->getQtyOrdered());
            $sourceItem->setSku($item->getSku());
            $sourceItem->setType($item->getProductType());
            $sourceItem->setProduct($item->getProduct());
            $sourceItem->setItem($item);

            $this->sourceItems[$item->getQuoteItemId()] = $sourceItem;

            if ($parentItem = $item->getParentItem()) {
                $sourceItem->setParent($this->generateSourceItem($parentItem));
            }
        }

        return $this->sourceItems[$item->getQuoteItemId()];
    }
}
