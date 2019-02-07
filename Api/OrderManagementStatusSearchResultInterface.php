<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api;

/**
 * OrderManagementStatus specific search results interface
 *
 * @api
 */
interface OrderManagementStatusSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get clients list
     *
     * @return \Qliro\QliroOne\Api\Data\OrderManagementStatusInterface[]
     */
    public function getItems();

    /**
     * Set clients list
     *
     * @param \Qliro\QliroOne\Api\Data\OrderManagementStatusInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
