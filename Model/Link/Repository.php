<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Link;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Qliro\QliroOne\Api\Data\LinkInterface;
use Qliro\QliroOne\Api\Data\LinkInterfaceFactory;
use Qliro\QliroOne\Api\LinkRepositoryInterface;
use Qliro\QliroOne\Model\ResourceModel\Link as LinkResourceModel;
use Qliro\QliroOne\Model\Link;
use Qliro\QliroOne\Model\ResourceModel\Link\Collection;
use Qliro\QliroOne\Api\LinkSearchResultInterfaceFactory;
use Qliro\QliroOne\Model\ResourceModel\Link\CollectionFactory;

/**
 * Link repository class
 *
 * @api
 */
class Repository implements LinkRepositoryInterface
{
    /**
     * @var \Qliro\QliroOne\Model\ResourceModel\Link
     */
    private $linkResourceModel;

    /**
     * @var \Qliro\QliroOne\Api\Data\LinkInterfaceFactory
     */
    private $linkFactory;

    /**
     * @var \Qliro\QliroOne\Api\LinkSearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @var \Qliro\QliroOne\Model\ResourceModel\Link\CollectionFactory
     */
    private $collectionFactory;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Model\ResourceModel\Link $linkResourceModel
     * @param \Qliro\QliroOne\Api\Data\LinkInterfaceFactory $linkFactory
     * @param \Qliro\QliroOne\Api\LinkSearchResultInterfaceFactory $searchResultFactory
     * @param \Qliro\QliroOne\Model\ResourceModel\Link\CollectionFactory $collectionFactory
     */
    public function __construct(
        LinkResourceModel $linkResourceModel,
        LinkInterfaceFactory $linkFactory,
        LinkSearchResultInterfaceFactory $searchResultFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->linkResourceModel = $linkResourceModel;
        $this->linkFactory = $linkFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Save a link
     *
     * @param \Qliro\QliroOne\Api\Data\LinkInterface $link
     * @return \Qliro\QliroOne\Api\Data\LinkInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(LinkInterface $link)
    {
        $this->linkResourceModel->save($link);

        return $link;
    }

    /**
     * Get a link by its ID
     *
     * @inheritdoc
     */
    public function get($id, $onlyActive = true)
    {
        return $this->getByField($id, null, $onlyActive);
    }

    /**
     * Get a link by Magento quote ID
     *
     * @inheritdoc
     */
    public function getByQuoteId($quoteId, $onlyActive = true)
    {
        return $this->getByField($quoteId, Link::FIELD_QUOTE_ID, $onlyActive);
    }

    /**
     * Get a link by Magento order ID
     *
     * @inheritdoc
     */
    public function getByOrderId($orderId, $onlyActive = true)
    {
        return $this->getByField($orderId, Link::FIELD_ORDER_ID, $onlyActive);
    }

    /**
     * Get a link by Qliro order ID
     *
     * @inheritdoc
     */
    public function getByQliroOrderId($qliroOrderId, $onlyActive = true)
    {
        return $this->getByField($qliroOrderId, Link::FIELD_QLIRO_ORDER_ID, $onlyActive);
    }

    /**
     * Get a link by Magento order ID
     *
     * @inheritdoc
     */
    public function getByReference($value, $onlyActive = true)
    {
        return $this->getByField($value, Link::FIELD_REFERENCE, $onlyActive);
    }

    /**
     * Delete a link
     *
     * @param \Qliro\QliroOne\Api\Data\LinkInterface $link
     * @return \Qliro\QliroOne\Model\ResourceModel\Link
     * @throws \Exception
     */
    public function delete(LinkInterface $link)
    {
        return $this->linkResourceModel->delete($link);
    }

    /**
     * Get a result of search among links by given search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Qliro\QliroOne\Api\LinkSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Qliro\QliroOne\Model\ResourceModel\Link\Collection $collection */
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
     * @param \Qliro\QliroOne\Model\ResourceModel\Link\Collection $collection
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
     * @param \Qliro\QliroOne\Model\ResourceModel\Link\Collection $collection
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
     * @param \Qliro\QliroOne\Model\ResourceModel\Link\Collection $collection
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
     * @param \Qliro\QliroOne\Model\ResourceModel\Link\Collection $collection
     * @return \Qliro\QliroOne\Api\LinkSearchResultInterface
     */
    private function buildSearchResult(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        /** @var \Qliro\QliroOne\Api\LinkSearchResultInterface $searchResults */
        $searchResults = $this->searchResultFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * Get a link by a specified field
     *
     * @param string|int $value
     * @param string $field
     * @param bool $onlyActive
     * @return \Qliro\QliroOne\Model\Link
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getByField($value, $field, $onlyActive = true)
    {
        /** @var \Qliro\QliroOne\Model\Link $link */
        if ($onlyActive) {
            $collection = $this->collectionFactory->create()
                ->addFieldToFilter($field, $value)
                ->addFieldToFilter(Link::FIELD_IS_ACTIVE, 1);
            $link = $collection->getFirstItem();
        } else {
            $link = $this->linkFactory->create();
            $this->linkResourceModel->load($link, $value, $field);
        }

        if (!$link->getId()) {
            throw new NoSuchEntityException(__('Cannot find a link with %1 = "%2"', $field, $value));
        }

        return $link;
    }
}
