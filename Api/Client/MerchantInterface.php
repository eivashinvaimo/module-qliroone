<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Client;

use Qliro\QliroOne\Api\Data\QliroOrderCreateRequestInterface;
use Qliro\QliroOne\Api\Data\QliroOrderUpdateRequestInterface;

/**
 * Merchant API client interface
 *
 * @api
 */
interface MerchantInterface
{
    /**
     * Perform QliroOne order creation
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderCreateRequestInterface $qliroOrderCreateRequest
     * @return int
     */
    public function createOrder(QliroOrderCreateRequestInterface $qliroOrderCreateRequest);

    /**
     * Get QliroOne order by its Qliro Order ID
     *
     * @param int $qliroOrderId
     * @return \Qliro\QliroOne\Api\Data\QliroOrderInterface
     */
    public function getOrder($qliroOrderId);

    /**
     * Update QliroOne order
     *
     * @param int $qliroOrderId
     * @param \Qliro\QliroOne\Api\Data\QliroOrderUpdateRequestInterface $qliroOrderUpdateRequest
     * @return void
     */
    public function updateOrder($qliroOrderId, QliroOrderUpdateRequestInterface $qliroOrderUpdateRequest);
}
