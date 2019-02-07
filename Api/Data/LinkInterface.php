<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * Quote/Order/QliroOne Order link interface
 *
 * @api
 */
interface LinkInterface
{
    const FIELD_ID = 'link_id';
    const FIELD_IS_ACTIVE = 'is_active';
    const FIELD_REFERENCE = 'reference';
    const FIELD_QUOTE_ID = 'quote_id';
    const FIELD_QLIRO_ORDER_ID = 'qliro_order_id';
    const FIELD_QLIRO_ORDER_STATUS = 'qliro_order_status';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_QUOTE_SNAPSHOT = 'quote_snapshot';
    const FIELD_REMOTE_IP = 'remote_ip';
    const FIELD_CREATED_AT = 'created_at';
    const FIELD_UPDATED_AT = 'updated_at';
    const FIELD_MESSAGE= 'message';

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Get "is_active" flag
     *
     * @return int
     */
    public function getIsActive();

    /**
     * Get unique reference hash
     *
     * @return int
     */
    public function getReference();

    /**
     * Get Magento quote ID
     *
     * @return int|null
     */
    public function getQuoteId();

    /**
     * Get Magento order ID
     *
     * @return int|null
     */
    public function getOrderId();

    /**
     * Get QliroOne order ID
     *
     * @return string|null
     */
    public function getQliroOrderId();

    /**
     * Get QliroOne order status
     *
     * @return string|null
     */
    public function getQliroOrderStatus();

    /**
     * Get client ip when link was created
     *
     * @return string
     */
    public function getRemoteIp();

    /**
     * Get creation timestamp
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Get timestamp of last update
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Get hash reflecting qliro order
     *
     * @return string
     */
    public function getQuoteSnapshot();

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage();

    /**
     * Set ID
     *
     * @param int $value
     * @return $this
     */
    public function setId($value);

    /**
     * Set "is_active" flag
     *
     * @var int $value
     * @return $this
     */
    public function setIsActive($value);

    /**
     * Set unique reference hash
     *
     * @var string $value
     * @return $this
     */
    public function setReference($value);

    /**
     * Set Magento quote ID
     *
     * @var int $value
     * @return $this
     */
    public function setQuoteId($value);

    /**
     * Set Magento order ID
     *
     * @var int $value
     * @return $this
     */
    public function setOrderId($value);

    /**
     * Set QliroOne order ID
     *
     * @var string $value
     * @return $this
     */
    public function setQliroOrderId($value);

    /**
     * Set QliroOne order status
     *
     * @var string $value
     * @return $this
     */
    public function setQliroOrderStatus($value);

    /**
     * Set client ip
     *
     * @var string $value
     * @return $this
     */
    public function setRemoteIp($value);

    /**
     * Set creation timestamp
     *
     * @var string $value
     * @return $this
     */
    public function setCreatedAt($value);

    /**
     * Set timestamp of last update
     *
     * @var string $value
     * @return $this
     */
    public function setUpdatedAt($value);

    /**
     * Set hash reflecting qliro order
     *
     * @var string $value
     * @return $this
     */
    public function setQuoteSnapshot($value);

    /**
     * Set message
     *
     * @var string $value
     * @return $this
     */
    public function setMessage($value);
}
