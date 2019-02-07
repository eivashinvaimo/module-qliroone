<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\ResourceModel;

use Qliro\QliroOne\Model\LogRecord as LogRecordModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Qliro\QliroOne\Api\Data\LogRecordInterface;

class LogRecord extends AbstractDb
{
    const TABLE_LOG = 'qliroone_log';
    const RECENT_EVENT = 60;    // when patching merchant reference, look this many seconds back for recent logging

    /**
     * LogRecord constructor.
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init(self::TABLE_LOG, LogRecordModel::FIELD_ID);
    }

    /**
     * When we have a merchantReference, we should patch any recent logging to ensure that the reference is present
     * on all log lines.
     *
     * @param string $merchantReference
     */
    public function patchMerchantReference($merchantReference)
    {
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $this->getConnection();

        $where = [
            sprintf("%s = ?", LogRecordInterface::FIELD_PROCESS_ID) => \getmypid(),
            sprintf("%s = ?", LogRecordInterface::FIELD_REFERENCE) => '',
            sprintf("%s >= NOW() - ?", LogRecordInterface::FIELD_DATE) => self::RECENT_EVENT
        ];
        try {
            $rows = $connection->update($this->getTable(
                self::TABLE_LOG),
                [LogRecordInterface::FIELD_REFERENCE => $merchantReference],
                $where);
        } catch (\Exception $e) {
        }
    }
}
