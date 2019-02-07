<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Product\Type\Handler;

use Qliro\QliroOne\Api\Product\TypeSourceItemInterface;

/**
 * Default product type handler class
 */
class ConfigurableHandler extends DefaultHandler
{
    /**
     * Prepare QliroOne order item's price
     *
     * @param \Qliro\QliroOne\Api\Product\TypeSourceItemInterface $item
     * @param bool $taxIncluded
     * @return float
     */
    public function preparePrice(TypeSourceItemInterface $item, $taxIncluded = true)
    {
        $parent = $item->getParent();

        return $taxIncluded ? $parent->getPriceInclTax() : $parent->getPriceExclTax();
    }

    /**
     * Prepare QliroOne order item's quantity
     *
     * @param \Qliro\QliroOne\Api\Product\TypeSourceItemInterface $item
     * @return int
     */
    public function prepareQuantity(TypeSourceItemInterface $item)
    {
        $parent = $item->getParent();

        return $parent->getQty();
    }

    /**
     * Prepare QliroOne order item's description
     *
     * @param \Qliro\QliroOne\Api\Product\TypeSourceItemInterface $item
     * @return string
     */
    public function prepareDescription(TypeSourceItemInterface $item)
    {
        return $item->getName();
    }
}
