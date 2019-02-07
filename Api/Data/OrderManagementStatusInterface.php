<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * OrderManagementStatus interface
 *
 * @api
 */
interface OrderManagementStatusInterface
{
    const FIELD_ID = 'id';
    const FIELD_DATE = 'date';
    const FIELD_TRANSACTION_ID = 'transaction_id';
    const FIELD_RECORD_TYPE = 'record_type';
    const FIELD_RECORD_ID = 'record_id';
    const FIELD_TRANSACTION_STATUS = 'transaction_status';
    const FIELD_MESSAGE = 'message';
    const FIELD_NOTIFICATION_STATUS = 'notification_status';
    const FIELD_QLIRO_ORDER_ID = 'qliro_order_id';

    /**
     * Magento record types initiating the notification
     */
    const RECORD_TYPE_SHIPMENT = 'shipment';
    const RECORD_TYPE_PAYMENT = 'payment';
    const RECORD_TYPE_CANCEL = 'cancel';

    /**
     * Internal status of order management status transaction update
     */
    const NOTIFICATION_STATUS_DONE = 'handled';
    const NOTIFICATION_STATUS_NEW = 'new';
    const NOTIFICATION_STATUS_ERROR = 'exception';
    const NOTIFICATION_STATUS_SKIPPED = 'skipped';

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getDate();

    /**
     * @return int
     */
    public function getTransactionId();

    /**
     * One of the defined record types declared above
     *
     * @return string
     */
    public function getRecordType();

    /**
     * @return int|null
     */
    public function getRecordId();

    /**
     * @return string
     */
    public function getTransactionStatus();

    /**
     * @return string
     */
    public function getNotificationStatus();

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return int
     */
    public function getQliroOrderId();

    /**
     * @var int $value
     * @return $this
     */
    public function setId($value);

    /**
     * @var string $value
     * @return $this
     */
    public function setDate($value);

    /**
     * @var int $value
     * @return $this
     */
    public function setTransactionId($value);

    /**
     * @var string $value
     * @return $this
     */
    public function setRecordType($value);

    /**
     * @var int $value
     * @return $this
     */
    public function setRecordId($value);

    /**
     * @var string $value
     * @return $this
     */
    public function setTransactionStatus($value);

    /**
     * @var string $value
     * @return $this
     */
    public function setNotificationStatus($value);

    /**
     * @var string $value
     * @return $this
     */
    public function setMessage($value);

    /**
     * @param int $id
     * @return $this
     */
    public function setQliroOrderId($id);
}
