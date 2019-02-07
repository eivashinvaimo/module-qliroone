<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Product;

use Qliro\QliroOne\Api\Data\QliroOrderItemInterface;

/**
 * Product Type Handler interface
 */
interface TypeHandlerInterface
{
    /**
     * Get QliroOne order item out of a source item, or null if not applicable
     *
     * @param \Qliro\QliroOne\Api\Product\TypeSourceItemInterface $item
     * @return \Qliro\QliroOne\Api\Data\QliroOrderItemInterface|null
     */
    public function getQliroOrderItem(TypeSourceItemInterface $item);

    /**
     * Get a reference to source item out of QliroOne order item, or null if not applicable
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterface $qliroOrderItem
     * @param \Qliro\QliroOne\Api\Product\TypeSourceProviderInterface $typeSourceProvider
     * @return \Qliro\QliroOne\Api\Product\TypeSourceItemInterface|null
     */
    public function getItem(QliroOrderItemInterface $qliroOrderItem, TypeSourceProviderInterface $typeSourceProvider);

    /**
     * Prepare QliroOne order item's merchant reference
     *
     * @param \Qliro\QliroOne\Api\Product\TypeSourceItemInterface $item
     * @return string
     */
    public function prepareMerchantReference(TypeSourceItemInterface $item);

    /**
     * Prepare QliroOne order item's price
     *
     * @param \Qliro\QliroOne\Api\Product\TypeSourceItemInterface $item
     * @param bool $taxIncluded
     * @return float
     */
    public function preparePrice(TypeSourceItemInterface $item, $taxIncluded = true);

    /**
     * Prepare QliroOne order item's quantity
     *
     * @param \Qliro\QliroOne\Api\Product\TypeSourceItemInterface $item
     * @return int
     */
    public function prepareQuantity(TypeSourceItemInterface $item);

    /**
     * Prepare QliroOne order item's description
     *
     * @param \Qliro\QliroOne\Api\Product\TypeSourceItemInterface $item
     * @return string
     */
    public function prepareDescription(TypeSourceItemInterface $item);

    /**
     * Prepare QliroOne order item's metadata
     *
     * @param \Qliro\QliroOne\Api\Product\TypeSourceItemInterface $item
     * @return array|null
     */
    public function prepareMetaData(TypeSourceItemInterface $item);
}
