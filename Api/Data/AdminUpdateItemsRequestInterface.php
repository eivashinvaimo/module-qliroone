<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * Admin Update Items Request interface
 */
interface AdminUpdateItemsRequestInterface extends ContainerInterface
{
    /**
     * @return string
     */
    public function getMerchantApiKey();

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @return \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[]
     */
    public function getOrderItems();

    /**
     * @return string
     */
    public function getRequestId();

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantApiKey($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setOrderId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCurrency($value);

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[] $value
     * @return $this
     */
    public function setOrderItems($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setRequestId($value);
}
