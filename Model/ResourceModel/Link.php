<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\ResourceModel;

use Qliro\QliroOne\Model\Link as LinkModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\DataObject;

class Link extends AbstractDb
{
    const TABLE_LINK = 'qliroone_link';

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init(self::TABLE_LINK, LinkModel::FIELD_ID);
    }

    /**
     * Update the timestamp on every save
     *
     * @param \Magento\Framework\DataObject $object
     */
    public function beforeSave(DataObject $object)
    {
        $object->setData('updated_at', new \Zend_Db_Expr('NOW()'));

        parent::beforeSave($object);
    }
}
