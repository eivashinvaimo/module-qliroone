<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\OrderManagementStatus;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Qliro\QliroOne\Api\Data\OrderManagementStatusInterface;
use Qliro\QliroOne\Api\Data\OrderManagementStatusInterfaceFactory;
use Qliro\QliroOne\Api\OrderManagementStatusRepositoryInterface;
use Qliro\QliroOne\Model\ResourceModel\OrderManagementStatus as OrderManagementStatusResourceModel;
use Qliro\QliroOne\Model\OrderManagementStatus;
use Qliro\QliroOne\Model\ResourceModel\OrderManagementStatus\Collection;
use Qliro\QliroOne\Api\OrderManagementStatusSearchResultInterfaceFactory;
use Qliro\QliroOne\Model\ResourceModel\OrderManagementStatus\CollectionFactory;

/**
 * OrderManagementStatus repository class
 *
 * @api
 */
class Repository implements OrderManagementStatusRepositoryInterface
{
    /**
     * @var \Qliro\QliroOne\Model\ResourceModel\OrderManagementStatus
     */
    private $OrderManagementStatusResourceModel;

    /**
     * @var \Qliro\QliroOne\Api\Data\OrderManagementStatusInterfaceFactory
     */
    private $OrderManagementStatusFactory;

    /**
     * @var \Qliro\QliroOne\Api\OrderManagementStatusSearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @var \Qliro\QliroOne\Model\ResourceModel\OrderManagementStatus\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Model\ResourceModel\OrderManagementStatus $OrderManagementStatusResourceModel
     * @param \Qliro\QliroOne\Api\Data\OrderManagementStatusInterfaceFactory $OrderManagementStatusFactory
     * @param \Qliro\QliroOne\Api\OrderManagementStatusSearchResultInterfaceFactory $searchResultFactory
     * @param \Qliro\QliroOne\Model\ResourceModel\OrderManagementStatus\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        OrderManagementStatusResourceModel $OrderManagementStatusResourceModel,
        OrderManagementStatusInterfaceFactory $OrderManagementStatusFactory,
        OrderManagementStatusSearchResultInterfaceFactory $searchResultFactory,
        CollectionFactory $collectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->OrderManagementStatusResourceModel = $OrderManagementStatusResourceModel;
        $this->OrderManagementStatusFactory = $OrderManagementStatusFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * Save a OrderManagementStatus
     *
     * @param \Qliro\QliroOne\Api\Data\OrderManagementStatusInterface $OrderManagementStatus
     * @return \Qliro\QliroOne\Api\Data\OrderManagementStatusInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(OrderManagementStatusInterface $OrderManagementStatus)
    {
        $this->OrderManagementStatusResourceModel->save($OrderManagementStatus);

        return $OrderManagementStatus;
    }

    /**
     * Get a OrderManagementStatus by its ID
     *
     * @inheritdoc
     */
    public function get($id)
    {
        return $this->getByField($id, null);
    }

    /**
     * Get parent by its transaction id. They might reuse their transaction ids, so I find the newest one
     *
     * @param int $id
     * @return \Qliro\QliroOne\Api\Data\OrderManagementStatusInterface|null
     */
    public function getParent($id)
    {
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        $sortOrder = $this->sortOrderBuilder->setField('date')->setDirection(SortOrder::SORT_DESC)->create();

        /** @var \Magento\Framework\Api\SearchCriteria $search */
        $search = $this->searchCriteriaBuilder
            ->addFilter('transaction_id',$id, 'eq')
            ->addFilter('record_type', 'null', 'neq')
            ->addSortOrder($sortOrder)
            ->create();

        $searchResult = $this->getList($search);
        foreach ($searchResult->getItems() as $parent) {
            return $parent;
        }

        return null;
    }

    /**
     * Get last transaction received of this transaction id, that was successfully handled
     *
     * @param int $id
     * @return \Qliro\QliroOne\Api\Data\OrderManagementStatusInterface|null
     */
    public function getPrevious($id)
    {
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        $sortOrder = $this->sortOrderBuilder->setField('date')->setDirection(SortOrder::SORT_DESC)->create();

        /** @var \Magento\Framework\Api\SearchCriteria $search */
        $search = $this->searchCriteriaBuilder
            ->addFilter('transaction_id',$id, 'eq')
            ->addFilter('notification_status',OrderManagementStatusInterface::NOTIFICATION_STATUS_DONE, 'eq')
            ->addSortOrder($sortOrder)
            ->create();

        $searchResult = $this->getList($search);
        foreach ($searchResult->getItems() as $previous) {
            return $previous;
        }

        return null;
    }

    /**
     * Delete a OrderManagementStatus
     *
     * @param \Qliro\QliroOne\Api\Data\OrderManagementStatusInterface $OrderManagementStatus
     * @return \Qliro\QliroOne\Model\ResourceModel\OrderManagementStatus
     * @throws \Exception
     */
    public function delete(OrderManagementStatusInterface $OrderManagementStatus)
    {
        return $this->OrderManagementStatusResourceModel->delete($OrderManagementStatus);
    }

    /**
     * Get a result of search among OrderManagementStatuss by given search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Qliro\QliroOne\Api\OrderManagementStatusSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Qliro\QliroOne\Model\ResourceModel\OrderManagementStatus\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPaginationToCollection($searchCriteria, $collection);

        $collection->load();

        return $this->buildSearchResult($searchCriteria, $collection);
    }

    /**
     * Add filters to collection
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param \Qliro\QliroOne\Model\ResourceModel\OrderManagementStatus\Collection $collection
     */
    private function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $fields[] = $filter->getField();
                $conditions[] = [$filter->getConditionType() => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * Add sort order to collection
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param \Qliro\QliroOne\Model\ResourceModel\OrderManagementStatus\Collection $collection
     */
    private function addSortOrdersToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
            $collection->addOrder($sortOrder->getField(), $direction);
        }
    }

    /**
     * Add pagination to collection
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param \Qliro\QliroOne\Model\ResourceModel\OrderManagementStatus\Collection $collection
     */
    private function addPaginationToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }

    /**
     * Build search result
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param \Qliro\QliroOne\Model\ResourceModel\OrderManagementStatus\Collection $collection
     * @return \Qliro\QliroOne\Api\OrderManagementStatusSearchResultInterface
     */
    private function buildSearchResult(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        /** @var \Qliro\QliroOne\Api\OrderManagementStatusSearchResultInterface $searchResults */
        $searchResults = $this->searchResultFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * Get a OrderManagementStatus by a specified field
     *
     * @param string|int $value
     * @param string $field
     * @return \Qliro\QliroOne\Model\OrderManagementStatus
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getByField($value, $field)
    {
        /** @var \Qliro\QliroOne\Model\OrderManagementStatus $OrderManagementStatus */
        $OrderManagementStatus = $this->OrderManagementStatusFactory->create();
        $this->OrderManagementStatusResourceModel->load($OrderManagementStatus, $value, $field);

        if (!$OrderManagementStatus->getId()) {
            throw new NoSuchEntityException(__('Cannot find a OrderManagementStatus with %1 = "%2"', $field, $value));
        }

        return $OrderManagementStatus;
    }
}
