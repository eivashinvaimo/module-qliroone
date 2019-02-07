<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model;

use Magento\Framework\Model\AbstractModel;
use Qliro\QliroOne\Api\Data\OrderManagementStatusInterface;

/**
 * OrderManagementStatus record model class
 */
class OrderManagementStatus extends AbstractModel implements OrderManagementStatusInterface
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\OrderManagementStatus::class);
    }

    /**
     * @return string
     */
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getData(self::FIELD_ID);
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->getData(self::FIELD_DATE);
    }

    /**
     * @return int
     */
    public function getTransactionId()
    {
        return $this->getData(self::FIELD_TRANSACTION_ID);
    }

    /**
     * One of the defined record types declared above
     *
     * @return string
     */
    public function getRecordType()
    {
        return $this->getData(self::FIELD_RECORD_TYPE);
    }

    /**
     * @return int|null
     */
    public function getRecordId()
    {
        return $this->getData(self::FIELD_RECORD_ID);
    }

    /**
     * @return string
     */
    public function getTransactionStatus()
    {
        return $this->getData(self::FIELD_TRANSACTION_STATUS);
    }

    /**
     * @return string
     */
    public function getNotificationStatus()
    {
        return $this->getData(self::FIELD_NOTIFICATION_STATUS);
    }

    /**
     * @inheritdoc
     */
    public function getMessage()
    {
        return $this->getData(self::FIELD_MESSAGE);
    }

    /**
     * @inheritdoc
     */
    public function getQliroOrderId()
    {
        return $this->getData(self::FIELD_QLIRO_ORDER_ID);
    }

    /**
     * @var string $value
     * @return $this
     */
    public function setDate($value)
    {
        return $this->setData(self::FIELD_DATE, $value);
    }

    /**
     * @var int $value
     * @return $this
     */
    public function setTransactionId($value)
    {
        return $this->setData(self::FIELD_TRANSACTION_ID, $value);
    }

    /**
     * @var string $value
     * @return $this
     */
    public function setRecordType($value)
    {
        return $this->setData(self::FIELD_RECORD_TYPE, $value);
    }

    /**
     * @var int $value
     * @return $this
     */
    public function setRecordId($value)
    {
        return $this->setData(self::FIELD_RECORD_ID, $value);
    }

    /**
     * @var string $value
     * @return $this
     */
    public function setTransactionStatus($value)
    {
        return $this->setData(self::FIELD_TRANSACTION_STATUS, $value);
    }

    /**
     * @var string $value
     * @return $this
     */
    public function setNotificationStatus($value)
    {
        return $this->setData(self::FIELD_NOTIFICATION_STATUS, $value);
    }

    /**
     * @inheritdoc
     */
    public function setMessage($value)
    {
        return $this->setData(self::FIELD_MESSAGE, $value);
    }

    /**
     * @inheritdoc
     */
    public function setQliroOrderId($value)
    {
        return $this->setData(self::FIELD_QLIRO_ORDER_ID, $value);
    }
}
