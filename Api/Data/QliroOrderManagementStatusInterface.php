<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * QliroOrderManagementStatus Notification interface
 *
 * @api
 */
interface QliroOrderManagementStatusInterface extends ContainerInterface
{
    const STATUS_CREATED = 'Created';
    const STATUS_USER_INTERACTION = 'UserInteractionRequired';
    const STATUS_INPROCESS = 'InProcess';
    const STATUS_ONHOLD = 'OnHold';
    const STATUS_SUCCESS = 'Success';
    const STATUS_ERROR = 'Error';
    const STATUS_CANCELLED = 'Cancelled';

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @return string
     */
    public function getMerchantReference();

    /**
     * @return int
     */
    public function getPaymentTransactionId();

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * Can return one of the statuses declared above
     *
     * @return string
     */
    public function getStatus();

    /**
     * @return string
     */
    public function getPaymentType();

    /**
     * @return string
     */
    public function getProviderTransactionId();

    /**
     * @return string
     */
    public function getProviderResultCode();

    /**
     * @return string
     */
    public function getProviderResultDescription();

    /**
     * @return string
     */
    public function getOriginalPaymentTransactionId();

    /**
     * @return int
     */
    public function getPaymentReference();

    /**
     * @return string
     */
    public function getTimestamp();

    /**
     * @param int $value
     * @return $this
     */
    public function setOrderId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantReference($value);

    /**
     * Can only be set to one of the statuses declared above
     *
     * @param string $value
     * @return $this
     */
    public function setStatus($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setPaymentTransactionId($value);

    /**
     * @param float $value
     * @return $this
     */
    public function setAmount($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCurrency($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setPaymentType($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setProviderTransactionId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setProviderResultCode($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setProviderResultDescription($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setOriginalPaymentTransactionId($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setPaymentReference($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setTimestamp($value);
}
