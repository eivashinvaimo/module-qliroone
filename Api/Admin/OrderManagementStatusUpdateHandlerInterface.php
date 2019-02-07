<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Admin;

/**
 * QliroOne Admin Order Management Status Update Transaction
 */
interface OrderManagementStatusUpdateHandlerInterface
{
    /**
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @param \Qliro\QliroOne\Model\OrderManagementStatus $omStatus
     */
    public function handleSuccess($qliroOrderManagementStatus, $omStatus);

    /**
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @param \Qliro\QliroOne\Model\OrderManagementStatus $omStatus
     */
    public function handleCancelled($qliroOrderManagementStatus, $omStatus);

    /**
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @param \Qliro\QliroOne\Model\OrderManagementStatus $omStatus
     */
    public function handleError($qliroOrderManagementStatus, $omStatus);

    /**
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @param \Qliro\QliroOne\Model\OrderManagementStatus $omStatus
     */
    public function handleInProcess($qliroOrderManagementStatus, $omStatus);

    /**
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @param \Qliro\QliroOne\Model\OrderManagementStatus $omStatus
     */
    public function handleOnHold($qliroOrderManagementStatus, $omStatus);

    /**
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @param \Qliro\QliroOne\Model\OrderManagementStatus $omStatus
     */
    public function handleUserInteraction($qliroOrderManagementStatus, $omStatus);

    /**
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @param \Qliro\QliroOne\Model\OrderManagementStatus $omStatus
     */
    public function handleCreated($qliroOrderManagementStatus, $omStatus);
}
