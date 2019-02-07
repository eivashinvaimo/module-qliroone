<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api;

use Qliro\QliroOne\Api\Data\CheckoutStatusInterface;
use Qliro\QliroOne\Api\Data\QliroOrderInterface;
use Qliro\QliroOne\Api\Data\UpdateShippingMethodsNotificationInterface;
use Qliro\QliroOne\Api\Data\ValidateOrderNotificationInterface;

/**
 * QliroOne Management interface
 *
 * @api
 */
interface ManagementInterface
{
    /**
     * Set quote to the Management class
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function setQuote($quote);

    /**
     * Fetch a QliroOne order and return it as a container
     *
     * @param bool $allowRecreate if the qliro order is stale, create a new one
     * @return \Qliro\QliroOne\Api\Data\QliroOrderInterface
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     */
    public function getQliroOrder($allowRecreate = true);

    /**
     * Fetch an HTML snippet from QliroOne order
     *
     * @return string
     */
    public function getHtmlSnippet();

    /**
     * Update quote with received data in the container and return a list of available shipping methods
     *
     * @param \Qliro\QliroOne\Api\Data\UpdateShippingMethodsNotificationInterface $updateContainer
     * @return \Qliro\QliroOne\Api\Data\UpdateShippingMethodsResponseInterface
     */
    public function getShippingMethods(UpdateShippingMethodsNotificationInterface $updateContainer);

    /**
     * Update quote with received data in the container and validate QliroOne order
     *
     * @param \Qliro\QliroOne\Api\Data\ValidateOrderNotificationInterface $validateContainer
     * @return \Qliro\QliroOne\Api\Data\ValidateOrderResponseInterface
     */
    public function validateQliroOrder(ValidateOrderNotificationInterface $validateContainer);

    /**
     * Get a QliroOne order, update the quote, then place Magento order
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderInterface $qliroOrder
     * @return \Magento\Sales\Model\Order|null|array
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     * @throws \Qliro\QliroOne\Model\Exception\FailToLockException
     */
    public function placeOrder(QliroOrderInterface $qliroOrder);

    /**
     * Poll for Magento order placement and return order increment ID if successful
     *
     * @return string
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     */
    public function pollPlaceOrder();

    /**
     * Process status push notification.
     * Place a Magento order as a result, or update existing if already placed
     *
     * @param \Qliro\QliroOne\Api\Data\CheckoutStatusInterface $checkoutStatus
     * @return \Qliro\QliroOne\Api\Data\CheckoutStatusResponseInterface
     */
    public function checkoutStatus(CheckoutStatusInterface $checkoutStatus);

    /**
     * Create payment transaction, which will hold and handle the Order Management features.
     * This saves payment and transaction, possibly also the order.
     *
     * @param \Magento\Sales\Model\Order $order
     * @param QliroOrderInterface $qliroOrder
     * @return void
     * @throws \Exception
     */
    public function createPaymentTransaction($order, $qliroOrder);

    /**
     * Update qliro order with information in quote
     *
     * @param int $orderId
     * @return void
     * @param bool $force
     */
    public function updateQliroOrder($orderId, $force = false);

    /**
     * Update customer with data from QliroOne frontend callback
     *
     * @param array $customerData
     * @return void
     * @throws \Exception
     */
    public function updateCustomer($customerData);

    /**
     * Update selected shipping method in quote.
     * Return true in case shipping method was set, or false if the quote is virtual or method was not changed
     *
     * @param string $code
     * @param string|null $secondaryOption
     * @param float|null $price
     * @return bool
     */
    public function updateShippingMethod($code, $secondaryOption = null, $price = null);

    /**
     * Update shipping price in quote.
     *
     * @param float|null $price
     * @return bool
     */
    public function updateShippingPrice($price);

    /**
     * Update selected shipping method in quote
     * Return true in case shipping method was set, or false if the quote is virtual or method was not changed
     *
     * @param float $fee
     * @return bool
     * @throws \Exception
     */
    public function updateFee($fee);

    /**
     * Cancel QliroOne order
     *
     * @param int $qliroOrderId
     * @return \Qliro\QliroOne\Api\Data\AdminTransactionResponseInterface
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     */
    public function cancelQliroOrder($qliroOrderId);

    /**
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return void
     * @throws \Exception
     */
    public function captureByInvoice($payment, $amount);

    /**
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return void
     * @throws \Exception
     */
    public function captureByShipment($shipment);

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderManagementStatusInterface $qliroOrderManagementStatus
     * @return \Qliro\QliroOne\Api\Data\QliroOrderManagementStatusResponseInterface
     * @throws \Exception
     */
    public function handleTransactionStatus($qliroOrderManagementStatus);

    /**
     * Get Admin Qliro order after it was already placed
     *
     * @param int $qliroOrderId
     * @return \Qliro\QliroOne\Api\Data\AdminOrderInterface
     */
    public function getAdminQliroOrder($qliroOrderId);
}
