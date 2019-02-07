<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model;

use Magento\Framework\Model\AbstractModel;
use Qliro\QliroOne\Api\Data\LogRecordInterface;

/**
 * Log record model class
 */
class LogRecord extends AbstractModel implements LogRecordInterface
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\LogRecord::class);
    }

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        return $this->getData(self::FIELD_DATE);
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
    public function getExtra()
    {
        return $this->getData(self::FIELD_EXTRA);
    }

    /**
     * @inheritdoc
     */
    public function getLevel()
    {
        return $this->getData(self::FIELD_LEVEL);
    }

    /**
     * @inheritdoc
     */
    public function getTag()
    {
        return $this->getData(self::FIELD_TAG);
    }

    /**
     * @inheritdoc
     */
    public function getProcessId()
    {
        return $this->getData(self::FIELD_PROCESS_ID);
    }

    /**
     * @inheritdoc
     */
    public function setDate($value)
    {
        return $this->setData(self::FIELD_DATE, $value);
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
    public function setLevel($value)
    {
        return $this->setData(self::FIELD_LEVEL, $value);
    }

    /**
     * @inheritdoc
     */
    public function setTag($value)
    {
        return $this->setData(self::FIELD_TAG, $value);
    }

    /**
     * @inheritdoc
     */
    public function setProcessId($value)
    {
        return $this->setData(self::FIELD_PROCESS_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function setExtra($value)
    {
        return $this->setData(self::FIELD_EXTRA, $value);
    }
}
