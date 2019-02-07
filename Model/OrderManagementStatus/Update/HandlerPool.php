<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\OrderManagementStatus\Update;

use Qliro\QliroOne\Api\Admin\OrderManagementStatusUpdateHandlerInterface;
use Qliro\QliroOne\Api\Data\QliroOrderManagementStatusInterface;
use Qliro\QliroOne\Model\Logger\Manager as LogManager;

/**
 * Class HandlerPool, all handlers available to deal with Order Management Status Notifications sent from Qliro
 */
class HandlerPool
{
    /**
     * @var array
     */
    private $handlerPool;

    /**
     * @var \Qliro\QliroOne\Model\Logger\Manager
     */
    private $logManager;

    private $handlerStatusMap = [
        QliroOrderManagementStatusInterface::STATUS_SUCCESS => 'handleSuccess',
        QliroOrderManagementStatusInterface::STATUS_CANCELLED => 'handleCancelled',
        QliroOrderManagementStatusInterface::STATUS_ERROR => 'handleError',
        QliroOrderManagementStatusInterface::STATUS_INPROCESS => 'handleInProcess',
        QliroOrderManagementStatusInterface::STATUS_ONHOLD => 'handleOnHold',
        QliroOrderManagementStatusInterface::STATUS_USER_INTERACTION => 'handleUserInteraction',
        QliroOrderManagementStatusInterface::STATUS_CREATED => 'handleCreated',
    ];

    /**
     * HandlerPool constructor.
     *
     * @param array $handlerPool
     * @param \Qliro\QliroOne\Model\Logger\Manager $logManager
     */
    public function __construct(
        LogManager $logManager,
        $handlerPool = []
    ) {
        $this->handlerPool = $handlerPool;
        $this->logManager = $logManager;
    }

    /**
     * If a handler is found, figure out what status it is and call the selected handler for it
     * Returns true if it was handled, otherwise it returns false
     *
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @param \Qliro\QliroOne\Model\OrderManagementStatus $omStatus
     * @return bool
     */
    public function handle($qliroOrderManagementStatus, $omStatus)
    {
        try {
            $type = $omStatus->getRecordType();
            $handler = $this->handlerPool[$type] ?? null;
            if ($handler instanceof OrderManagementStatusUpdateHandlerInterface) {
                $handlerFunction = $this->handlerStatusMap[$qliroOrderManagementStatus->getStatus()];
                if ($handlerFunction) {
                    $handler->$handlerFunction($qliroOrderManagementStatus, $omStatus);
                } else {
                    throw new \LogicException('No status function for OrderManagementStatus handler available');
                }
            } else {
                throw new \LogicException('No Handler for OrderManagementStatus available');
            }

        } catch (\Exception $exception) {
            $this->logManager->debug(
                $exception,
                [
                    'extra' => [
                        'type' => $type,
                        'status' => $qliroOrderManagementStatus->getStatus(),
                    ],
                ]
            );

            return false;
        }

        return true;
    }
}