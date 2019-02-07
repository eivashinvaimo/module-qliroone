<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Product;

/**
 * Product Type Source Provider interface
 */
interface TypeSourceProviderInterface
{
    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param string $reference
     * @return \Qliro\QliroOne\Api\Product\TypeSourceItemInterface
     */
    public function getSourceItemByMerchantReference($reference);

    /**
     * Seems to not be used, in any provider...
     *
     * @return \Qliro\QliroOne\Api\Product\TypeSourceItemInterface[]
     */
    public function getSourceItems();

    /**
     * @param mixed $item
     * @return \Qliro\QliroOne\Api\Product\TypeSourceItemInterface
     */
    public function generateSourceItem($item);
}
