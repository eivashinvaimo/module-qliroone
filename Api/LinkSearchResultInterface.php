<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api;

/**
 * Link specific search results interface
 *
 * @api
 */
interface LinkSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get clients list
     *
     * @return \Qliro\QliroOne\Api\Data\LinkInterface[]
     */
    public function getItems();

    /**
     * Set clients list
     *
     * @param \Qliro\QliroOne\Api\Data\LinkInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
