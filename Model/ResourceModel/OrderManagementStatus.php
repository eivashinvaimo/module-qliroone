<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\ResourceModel;

use Qliro\QliroOne\Model\OrderManagementStatus as OrderManagementStatusModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\DataObject;

class OrderManagementStatus extends AbstractDb
{
    const TABLE_OM_STATUS = 'qliroone_om_status';

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
        $this->_init(self::TABLE_OM_STATUS, OrderManagementStatusModel::FIELD_ID);
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
