<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Qliro\QliroOne\Api\Data\OrderManagementStatusInterface;

/**
 * OrderManagementStatus repository interface
 *
 * @api
 */
interface OrderManagementStatusRepositoryInterface
{
    /**
     * Get a status by its ID
     *
     * @param int $id
     * @return \Qliro\QliroOne\Api\Data\OrderManagementStatusInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * Get parent by its transaction id
     *
     * @param int $id
     * @return \Qliro\QliroOne\Api\Data\OrderManagementStatusInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getParent($id);

    /**
     * Get last transaction received of this transaction id
     *
     * @param int $id
     * @return \Qliro\QliroOne\Api\Data\OrderManagementStatusInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPrevious($id);

    /**
     * Save a OM status
     *
     * @param \Qliro\QliroOne\Api\Data\OrderManagementStatusInterface $OrderManagementStatus
     * @return \Qliro\QliroOne\Api\Data\OrderManagementStatusInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(OrderManagementStatusInterface $OrderManagementStatus);

    /**
     * Delete a OM status
     *
     * @param \Qliro\QliroOne\Api\Data\OrderManagementStatusInterface $OrderManagementStatus
     * @return $this
     */
    public function delete(OrderManagementStatusInterface $OrderManagementStatus);

    /**
     * Get a result of search among OrderManagementStatuss by given search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Qliro\QliroOne\Api\OrderManagementStatusSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
