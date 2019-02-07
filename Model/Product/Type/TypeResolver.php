<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Product\Type;

use Qliro\QliroOne\Api\Data\QliroOrderItemInterface;
use Qliro\QliroOne\Api\Product\TypeSourceProviderInterface;
use Qliro\QliroOne\Model\Product\ProductPool;

/**
 * Item product type resolver class
 */
class TypeResolver
{
    /**
     * @var \Qliro\QliroOne\Model\Product\ProductPool
     */
    private $productPool;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Model\Product\ProductPool $productPool
     */
    public function __construct(
        ProductPool $productPool
    ) {
        $this->productPool = $productPool;
    }

    /**
     * Resolve product type from a QliroOne order item
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterface $qliroOrderItem
     * @param \Qliro\QliroOne\Api\Product\TypeSourceProviderInterface $typeSourceProvider
     * @return string|null
     */
    public function resolve(QliroOrderItemInterface $qliroOrderItem, TypeSourceProviderInterface $typeSourceProvider)
    {
        if ($qliroOrderItem->getType() !== QliroOrderItemInterface::TYPE_PRODUCT) {
            return null;
        }

        $sourceItem = $typeSourceProvider->getSourceItemByMerchantReference($qliroOrderItem->getMerchantReference());

        if ($sourceItem) {
            $typeHash = [$sourceItem->getProduct()->getTypeId()];

            if ($parentItem = $sourceItem->getParent()) {
                $typeHash[] = $parentItem->getProduct()->getTypeId();
            }

            return implode(':', $typeHash);
        }

        return null;
    }
}
