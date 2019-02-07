<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

// @codingStandardsIgnoreFile
// phpcs:ignoreFile

namespace Qliro\QliroOne\Model\ResourceModel\OrderManagementStatus;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Qliro\QliroOne\Model\OrderManagementStatus as OrderManagementStatusModel;
use Qliro\QliroOne\Model\ResourceModel\OrderManagementStatus as OrderManagementStatusResource;

/**
 * OrderManagementStatus collection class
 */
class Collection extends AbstractCollection
{
    /**
     * Collection initialization
     */
    protected function _construct()
    {
        $this->_init(OrderManagementStatusModel::class, OrderManagementStatusResource::class);
    }
}
