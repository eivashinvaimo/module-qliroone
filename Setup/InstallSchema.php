<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

// @codingStandardsIgnoreFile
// phpcs:ignoreFile

namespace Qliro\QliroOne\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Qliro\QliroOne\Api\Data\CheckoutStatusInterface;
use Qliro\QliroOne\Model\LogRecord as LogRecordModel;
use Qliro\QliroOne\Model\ResourceModel\LogRecord;
use Qliro\QliroOne\Model\Link as LinkModel;
use Qliro\QliroOne\Model\ResourceModel\Link;
use Qliro\QliroOne\Model\ResourceModel\Lock;
use Qliro\QliroOne\Model\OrderManagementStatus as OrderManagementStatusModel;
use Qliro\QliroOne\Model\ResourceModel\OrderManagementStatus;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (!$installer->tableExists(LogRecord::TABLE_LOG)) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable(LogRecord::TABLE_LOG))
                ->addColumn(
                    LogRecordModel::FIELD_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Log line id'
                )
                ->addColumn(
                    LogRecordModel::FIELD_DATE,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Date'
                )
                ->addColumn(LogRecordModel::FIELD_LEVEL, Table::TYPE_TEXT, 32, ['nullable' => false], 'Log level')
                ->addColumn(
                    LogRecordModel::FIELD_PROCESS_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Process ID'
                )
                ->addColumn(
                    LogRecordModel::FIELD_REFERENCE,
                    Table::TYPE_TEXT,
                    25,
                    [],
                    'Merchant ID')
                ->addColumn(
                    LogRecordModel::FIELD_TAGS,
                    Table::TYPE_TEXT,
                    256,
                    ['unsigned' => true],
                    'Comma separated list of tags')
                ->addColumn(LogRecordModel::FIELD_MESSAGE, Table::TYPE_TEXT, null, [], 'Message')
                ->addColumn(LogRecordModel::FIELD_EXTRA, Table::TYPE_TEXT, null, [], 'Extra data')
                ->addIndex(
                    $installer->getIdxName(LogRecord::TABLE_LOG, [LogRecordModel::FIELD_DATE]),
                    [LogRecordModel::FIELD_DATE]
                )
                ->addIndex(
                    $installer->getIdxName(LogRecord::TABLE_LOG, [LogRecordModel::FIELD_LEVEL]),
                    [LogRecordModel::FIELD_LEVEL]
                )
                ->addIndex(
                    $installer->getIdxName(LogRecord::TABLE_LOG, [LogRecordModel::FIELD_REFERENCE]),
                    [LogRecordModel::FIELD_REFERENCE]
                )
                ->addIndex(
                    $installer->getIdxName(LogRecord::TABLE_LOG, [LogRecordModel::FIELD_PROCESS_ID]),
                    [LogRecordModel::FIELD_PROCESS_ID]
                )
                ->setComment('QliroOne log');
            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists(Link::TABLE_LINK)) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable(Link::TABLE_LINK))
                ->addColumn(
                    LinkModel::FIELD_ID,
                    Table::TYPE_INTEGER,
                    10,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Link ID'
                )->addColumn(
                    LinkModel::FIELD_IS_ACTIVE,
                    Table::TYPE_SMALLINT,
                    1,
                    ['unsigned' => true, 'default' => 1, 'nullable' => false],
                    'Flag indicating if link is still in used'
                )
                ->addColumn(
                    LinkModel::FIELD_REFERENCE,
                    Table::TYPE_TEXT,
                    25,
                    ['nullable' => false],
                    'Unique QliroOne order merchant reference'
                )
                ->addColumn(
                    LinkModel::FIELD_QUOTE_ID,
                    Table::TYPE_INTEGER,
                    10,
                    ['unsigned' => true],
                    'Quote ID, null when order has been created'
                )
                ->addColumn(
                    LinkModel::FIELD_QLIRO_ORDER_ID,
                    Table::TYPE_INTEGER,
                    12,
                    ['unsigned' => true],
                    'QliroOne Order ID'
                )
                ->addColumn(
                    LinkModel::FIELD_QLIRO_ORDER_STATUS,
                    Table::TYPE_TEXT,
                    32,
                    ['default' => CheckoutStatusInterface::STATUS_IN_PROCESS],
                    'QliroOne Order Status'
                )
                ->addColumn(
                    LinkModel::FIELD_ORDER_ID,
                    Table::TYPE_INTEGER,
                    10,
                    ['unsigned' => true],
                    'Order ID, null before order has been created'
                )
                ->addColumn(
                    LinkModel::FIELD_QUOTE_SNAPSHOT,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Quote snapshot signature'
                )
                ->addColumn(
                    LinkModel::FIELD_REMOTE_IP,
                    Table::TYPE_TEXT,
                    null,
                    [],
                    'Client IP when link was created'
                )
                ->addColumn(
                    LinkModel::FIELD_CREATED_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Link creation timestamp'
                )
                ->addColumn(
                    LinkModel::FIELD_UPDATED_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Link last update timestamp'
                )
                ->addColumn(
                    LinkModel::FIELD_MESSAGE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Latest message or error message'
                )
                ->addIndex(
                    $installer->getIdxName(Link::TABLE_LINK, [LinkModel::FIELD_IS_ACTIVE]),
                    [LinkModel::FIELD_IS_ACTIVE]
                )
                ->addIndex(
                    $installer->getIdxName(
                        Link::TABLE_LINK,
                        [LinkModel::FIELD_REFERENCE],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    [LinkModel::FIELD_REFERENCE],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addIndex(
                    $installer->getIdxName(Link::TABLE_LINK, [LinkModel::FIELD_QUOTE_ID]),
                    [LinkModel::FIELD_QUOTE_ID]
                )
                ->addIndex(
                    $installer->getIdxName(Link::TABLE_LINK, [LinkModel::FIELD_QLIRO_ORDER_ID]),
                    [LinkModel::FIELD_QLIRO_ORDER_ID]
                )
                ->addIndex(
                    $installer->getIdxName(Link::TABLE_LINK, [LinkModel::FIELD_ORDER_ID]),
                    [LinkModel::FIELD_ORDER_ID]
                )
                ->addIndex(
                    $installer->getIdxName(Link::TABLE_LINK, [LinkModel::FIELD_CREATED_AT]),
                    [LinkModel::FIELD_CREATED_AT]
                )
                ->addIndex(
                    $installer->getIdxName(Link::TABLE_LINK, [LinkModel::FIELD_UPDATED_AT]),
                    [LinkModel::FIELD_UPDATED_AT]
                )
                ->setComment('Link QliroOne orders with Magento');
            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists(Lock::TABLE_LOCK)) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable(Lock::TABLE_LOCK))
                ->addColumn(
                    Lock::FIELD_ID,
                    Table::TYPE_INTEGER,
                    12,
                    ['unsigned' => true, 'nullable' => false],
                    'QliroOne Order ID'
                )
                ->addColumn(
                    Lock::FIELD_CREATED_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Timestamp when lock was created'
                )
                ->addColumn(
                    Lock::FIELD_PROCESS_ID,
                    Table::TYPE_INTEGER,
                    8,
                    ['unsigned' => true, 'nullable' => false],
                    'PID'
                )
                ->addIndex(
                    $installer->getIdxName(Lock::TABLE_LOCK, [Lock::FIELD_ID], AdapterInterface::INDEX_TYPE_UNIQUE),
                    [Lock::FIELD_ID],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addIndex(
                    $installer->getIdxName(Lock::TABLE_LOCK, [Lock::FIELD_CREATED_AT]),
                    [Lock::FIELD_CREATED_AT]
                )
                ->setComment('Lock for creating Magento order');
            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists(OrderManagementStatus::TABLE_OM_STATUS)) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable(OrderManagementStatus::TABLE_OM_STATUS))
                ->addColumn(
                    OrderManagementStatusModel::FIELD_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )
                ->addColumn(
                    OrderManagementStatusModel::FIELD_DATE,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Date'
                )
                ->addColumn(
                    OrderManagementStatusModel::FIELD_TRANSACTION_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Payment Transaction ID'
                )
                ->addColumn(
                    OrderManagementStatusModel::FIELD_RECORD_TYPE,
                    Table::TYPE_TEXT,
                    25,
                    [],
                    'Record Type'
                )
                ->addColumn(
                    OrderManagementStatusModel::FIELD_RECORD_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Record ID'
                )
                ->addColumn(
                    OrderManagementStatusModel::FIELD_TRANSACTION_STATUS,
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Transaction Status'
                )
                ->addColumn(
                    OrderManagementStatusModel::FIELD_NOTIFICATION_STATUS,
                    Table::TYPE_TEXT,
                    10,
                    [],
                    'Notification Status'
                )
                ->addColumn(
                    OrderManagementStatusModel::FIELD_MESSAGE,
                    Table::TYPE_TEXT,
                    null,
                    [],
                    'Possible Message'
                )
                ->addColumn(
                    OrderManagementStatusModel::FIELD_QLIRO_ORDER_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Qliro Order Id'
                )->addIndex(
                    $installer->getIdxName(
                        OrderManagementStatus::TABLE_OM_STATUS,
                        [OrderManagementStatusModel::FIELD_DATE]
                    ),
                    [OrderManagementStatusModel::FIELD_DATE]
                )->addIndex(
                    $installer->getIdxName(
                        OrderManagementStatus::TABLE_OM_STATUS,
                        [OrderManagementStatusModel::FIELD_TRANSACTION_ID]
                    ),
                    [OrderManagementStatusModel::FIELD_TRANSACTION_ID]
                )->addIndex(
                    $installer->getIdxName(
                        OrderManagementStatus::TABLE_OM_STATUS,
                        [OrderManagementStatusModel::FIELD_TRANSACTION_STATUS]
                    ),
                    [OrderManagementStatusModel::FIELD_TRANSACTION_STATUS]
                )->addIndex(
                    $installer->getIdxName(
                        OrderManagementStatus::TABLE_OM_STATUS,
                        [OrderManagementStatusModel::FIELD_RECORD_ID]
                    ),
                    [OrderManagementStatusModel::FIELD_RECORD_ID]
                )
                ->setComment('QliroOne OM Notification Statuses');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
