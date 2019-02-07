<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Client;

use Qliro\QliroOne\Api\Data\AdminCancelOrderRequestInterface;
use Qliro\QliroOne\Api\Data\AdminMarkItemsAsShippedRequestInterface;
use Qliro\QliroOne\Api\Data\AdminReturnWithItemsRequestInterface;

/**
 * Order Management API client interface
 *
 * @api
 */
interface OrderManagementInterface
{
    /**
     * Get QliroOne order by its Qliro Order ID
     *
     * @param int $qliroOrderId
     * @return \Qliro\QliroOne\Api\Data\AdminOrderInterface
     * @throws \Qliro\QliroOne\Model\Api\Client\Exception\ClientException
     */
    public function getOrder($qliroOrderId);

    /**
     * Send a "Mark items as shipped" request
     *
     * @param \Qliro\QliroOne\Api\Data\AdminMarkItemsAsShippedRequestInterface $request
     * @return \Qliro\QliroOne\Api\Data\AdminTransactionResponseInterface
     * @throws \Qliro\QliroOne\Model\Api\Client\Exception\ClientException
     */
    public function markItemsAsShipped(AdminMarkItemsAsShippedRequestInterface $request);

    /**
     * Cancel admin QliroOne order
     *
     * @param \Qliro\QliroOne\Api\Data\AdminCancelOrderRequestInterface $request
     * @return \Qliro\QliroOne\Api\Data\AdminTransactionResponseInterface
     * @throws \Qliro\QliroOne\Model\Api\Client\Exception\ClientException
     */
    public function cancelOrder(AdminCancelOrderRequestInterface $request);

    /**
     * Make a call "Return with items"
     *
     * @param \Qliro\QliroOne\Api\Data\AdminReturnWithItemsRequestInterface $request
     * @return \Qliro\QliroOne\Api\Data\AdminTransactionResponseInterface
     * @throws \Qliro\QliroOne\Model\Api\Client\Exception\ClientException
     */
    public function returnWithItems(AdminReturnWithItemsRequestInterface $request);

    /**
     * Get admin QliroOne order payment transaction
     *
     * @param int $paymentTransactionId
     * @return \Qliro\QliroOne\Api\Data\AdminOrderPaymentTransactionInterface
     * @throws \Qliro\QliroOne\Model\Api\Client\Exception\ClientException
     */
    public function getPaymentTransaction($paymentTransactionId);

    /**
     * Retry a reversal payment
     *
     * @param int $paymentReference
     * @return \Qliro\QliroOne\Api\Data\AdminOrderPaymentTransactionInterface|null
     * @throws \Qliro\QliroOne\Model\Api\Client\Exception\ClientException
     */
    public function retryReversalPayment($paymentReference);
}
