<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Qliro\QliroOne\Api\Data\LinkInterface;

/**
 * Link repository interface
 *
 * @api
 */
interface LinkRepositoryInterface
{
    /**
     * Get a link by its ID
     *
     * @param int $id
     * @param bool $onlyActive
     * @return \Qliro\QliroOne\Api\Data\LinkInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id, $onlyActive = true);

    /**
     * Get a link by Magento quote ID
     *
     * @param int $quoteId
     * @param bool $onlyActive
     * @return \Qliro\QliroOne\Api\Data\LinkInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByQuoteId($quoteId, $onlyActive = true);

    /**
     * Get a link by Magento order ID
     *
     * @param int $orderId
     * @param bool $onlyActive
     * @return \Qliro\QliroOne\Api\Data\LinkInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByOrderId($orderId, $onlyActive = true);

    /**
     * Get a link by Qliro order ID
     *
     * @param int $qliroOrderId
     * @param bool $onlyActive
     * @return \Qliro\QliroOne\Api\Data\LinkInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByQliroOrderId($qliroOrderId, $onlyActive = true);

    /**
     * Get a link by generated order reference
     *
     * @param string $value
     * @param bool $onlyActive
     * @return \Qliro\QliroOne\Api\Data\LinkInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByReference($value, $onlyActive = true);

    /**
     * Save a link
     *
     * @param \Qliro\QliroOne\Api\Data\LinkInterface $link
     * @return \Qliro\QliroOne\Api\Data\LinkInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(LinkInterface $link);

    /**
     * Delete a link
     *
     * @param \Qliro\QliroOne\Api\Data\LinkInterface $link
     * @return $this
     */
    public function delete(LinkInterface $link);

    /**
     * Get a result of search among links by given search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Qliro\QliroOne\Api\LinkSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
