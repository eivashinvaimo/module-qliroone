<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

// @codingStandardsIgnoreFile
// phpcs:ignoreFile

namespace Qliro\QliroOne\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Qliro\QliroOne\Model\Logger\Manager as LogManager;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Qliro\QliroOne\Helper\Data as Helper;

class Lock extends AbstractDb
{
    const TABLE_LOCK = 'qliroone_order_lock';

    const FIELD_ID = 'qliro_order_id';
    const FIELD_CREATED_AT = 'created_at';
    const FIELD_PROCESS_ID = 'process_id';

    const LOCK_EXPIRATION = 10;  /* retire locks after x minutes */

    /**
     * @var \Qliro\QliroOne\Model\Logger\Manager
     */
    private $logManager;

    /**
     * Inject dependencies
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Qliro\QliroOne\Model\Logger\Manager $logManager
     * @param \Qliro\QliroOne\Helper\Data $helper
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        LogManager $logManager,
        Helper $helper,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->logManager = $logManager;
        $this->helper = $helper;
    }

    /**
     * @var \Qliro\QliroOne\Helper\Data
     */
    private $helper;

    /**
     * Dummy init method
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_LOCK, self::FIELD_ID);
    }

    /**
     * Perform a lock and check result.
     * - true, the lock was successful
     * - false, the lock has failed
     *
     * @param string $qliroOrderId
     * @param bool $checkProcess
     * @return bool
     */
    public function lock($qliroOrderId, $checkProcess = true)
    {
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $this->getConnection();

        $retireAfter = self::LOCK_EXPIRATION * 60;

        $where = [
            sprintf('%s < NOW() - ?' , self::FIELD_CREATED_AT) => $retireAfter
        ];
        $rows = $connection->delete($this->getTable(self::TABLE_LOCK), $where);

        if ($rows > 0) {
            $this->logManager->notice('lock: retired {count} locks', ['count' => $rows]);
        }

        try {
            $rows = $connection->insert($this->getTable(self::TABLE_LOCK), [
                self::FIELD_ID => $qliroOrderId,
                self::FIELD_PROCESS_ID => $this->helper->getPid(),
                self::FIELD_CREATED_AT => new \Zend_Db_Expr('NOW()')
            ]);
        } catch (\Exception $e) {
            if ($checkProcess) {
                $select = $connection->select()
                    ->from($this->getTable(self::TABLE_LOCK), self::FIELD_PROCESS_ID)
                    ->where(sprintf('%s = :id', self::FIELD_ID ));
                $row = $connection->fetchRow($select, [':id' => $qliroOrderId]);
                if (!empty($row[self::FIELD_PROCESS_ID])) {
                    $pid = $row[self::FIELD_PROCESS_ID];
                    if (!$this->helper->isProcessAlive($pid)) {
                        $rows = $this->unlock($qliroOrderId, true);
                        if ($rows > 0) {
                            $this->logManager->notice('lock: retired lock for dead process {pid}', ['pid' => $pid]);
                        }

                        return $this->lock($qliroOrderId, false);
                    }
                }
            }

            return false;
        }

        return $rows > 0;
    }

    /**
     * Perform an unlock and check result.
     * - true, the unlock was successful
     * - false, the unlock has failed
     *
     * @param string $qliroOrderId
     * @param bool $forced  Attempt to remove lock even if this is a different process.
     * @return bool
     */
    public function unlock($qliroOrderId, $forced = false)
    {
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $this->getConnection();

        $where = [sprintf('%s = ?', self::FIELD_ID) => $qliroOrderId];
        if (!$forced) {
            $where[sprintf('%s = ?', self::FIELD_PROCESS_ID)] = $this->helper->getPid();
        }
        try {
            $rows = $connection->delete($this->getTable(self::TABLE_LOCK), $where);
        } catch (\Exception $e) {
            return false;
        }
        if ($rows == 0) {
            $this->logManager->notice('unlock: no lock found for {id}', ['id' => $qliroOrderId]);

            return false;
        }

        return true;
    }
}
