<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model;

use Magento\Framework\Model\AbstractModel;
use Qliro\QliroOne\Api\Data\LinkInterface;

/**
 * Link record model class
 */
class Link extends AbstractModel implements LinkInterface
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Link::class);
    }

    /**
     * @return string
     */
    /**
     * @inheritdoc
     */
    public function getLinkId()
    {
        return $this->getData(self::FIELD_ID);
    }

    /**
     * @inheritdoc
     */
    public function getIsActive()
    {
        return (bool)$this->getData(self::FIELD_IS_ACTIVE);
    }

    /**
     * @inheritdoc
     */
    public function getReference()
    {
        return $this->getData(self::FIELD_REFERENCE);
    }

    /**
     * @inheritdoc
     */
    public function getQuoteId()
    {
        return $this->getData(self::FIELD_QUOTE_ID);
    }

    /**
     * @inheritdoc
     */
    public function getQliroOrderId()
    {
        return $this->getData(self::FIELD_QLIRO_ORDER_ID);
    }

    /**
     * @inheritdoc
     */
    public function getQliroOrderStatus()
    {
        return $this->getData(self::FIELD_QLIRO_ORDER_STATUS);
    }

    /**
     * @inheritdoc
     */
    public function getOrderId()
    {
        return $this->getData(self::FIELD_ORDER_ID);
    }

    /**
     * @inheritdoc
     */
    public function getRemoteIp()
    {
        return $this->getData(self::FIELD_REMOTE_IP);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::FIELD_CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::FIELD_UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function getQuoteSnapshot() {

        return $this->getData(self::FIELD_QUOTE_SNAPSHOT);
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
    public function setIsActive($value)
    {
        return $this->setData(self::FIELD_IS_ACTIVE, $value);
    }

    /**
     * @inheritdoc
     */
    public function setReference($value)
    {
        return $this->setData(self::FIELD_REFERENCE, $value);
    }

    /**
     * @inheritdoc
     */
    public function setQuoteId($value)
    {
        return $this->setData(self::FIELD_QUOTE_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function setQliroOrderId($value)
    {
        return $this->setData(self::FIELD_QLIRO_ORDER_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function setQliroOrderStatus($value)
    {
        return $this->setData(self::FIELD_QLIRO_ORDER_STATUS, $value);
    }

    /**
     * @inheritdoc
     */
    public function setOrderId($value)
    {
        return $this->setData(self::FIELD_ORDER_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function setRemoteIp($value)
    {
        return $this->setData(self::FIELD_REMOTE_IP, $value);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::FIELD_CREATED_AT, $value);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($value)
    {
        return $this->setData(self::FIELD_UPDATED_AT, $value);
    }

    /**
     * @inheritdoc
     */
    public function setQuoteSnapshot($value)
    {
        return $this->setData(self::FIELD_QUOTE_SNAPSHOT, $value);
    }

    /**
     * @inheritdoc
     */
    public function setMessage($value)
    {
        return $this->setData(self::FIELD_MESSAGE, $value);
    }
}
