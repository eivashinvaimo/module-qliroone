<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

// @codingStandardsIgnoreFile
// phpcs:ignoreFile

namespace Qliro\QliroOne\Model\ResourceModel\Link;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Qliro\QliroOne\Model\Link as LinkModel;
use Qliro\QliroOne\Model\ResourceModel\Link as LinkResource;

/**
 * Link collection class
 */
class Collection extends AbstractCollection
{
    /**
     * Collection initialization
     */
    protected function _construct()
    {
        $this->_init(LinkModel::class, LinkResource::class);
    }
}
