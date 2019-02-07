<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\OrderManagementStatus\Update\Handler;

use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use Qliro\QliroOne\Api\Admin\OrderManagementStatusUpdateHandlerInterface;
use Magento\Sales\Model\Order;
use Qliro\QliroOne\Model\Exception\TerminalException;
use Qliro\QliroOne\Model\Logger\Manager;

class Shipment implements OrderManagementStatusUpdateHandlerInterface
{
    /**
     * @var \Magento\Sales\Api\ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    /**
     * @var Order\Payment\Transaction\BuilderInterface
     */
    private $transactionBuilder;

    /**
     * @var \Qliro\QliroOne\Model\Logger\Manager
     */
    private $logManager;

    /**
     * Shipment constructor.
     * @param \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder
     * @param \Qliro\QliroOne\Model\Logger\Manager $logManager
     */
    public function __construct(
        ShipmentRepositoryInterface $shipmentRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceRepositoryInterface $invoiceRepository,
        BuilderInterface $transactionBuilder,
        Manager $logManager
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->transactionBuilder = $transactionBuilder;
        $this->logManager = $logManager;
    }

    /**
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @param \Qliro\QliroOne\Model\OrderManagementStatus $omStatus
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     */
    public function handleSuccess($qliroOrderManagementStatus, $omStatus)
    {

        try {
            $shipment = $this->getShipment($omStatus);
            $order = $shipment->getOrder();
            $payment = $order->getPayment();

            /*
             * Update Order
             */
            if ($order->getState() == Order::STATE_HOLDED) {
                $order->unhold();
                $this->orderRepository->save($order);
            }

            /*
             * Create Invoice
             */
            $invoiceItems = [];
            $shipmentItems = $shipment->getAllItems();

            /** @var \Magento\Sales\Model\Order\Shipment\Item $shipmentItem */
            foreach ($shipmentItems as $shipmentItem) {
                $qty = (int)$shipmentItem->getQty();

                /** @var \Magento\Sales\Model\Order\Item $item */
                $item = $order->getItemById($shipmentItem->getOrderItemId());

                /*
                 * This is the same test for invoice made earlier, as seen in:
                 * \Qliro\QliroOne\Model\QliroOrder\Admin\Builder\ShipmentOrderItemsBuilder::create
                 */
                if ($item->getQtyInvoiced() > 0) {
                    $remaining = $item->getQtyOrdered() - $item->getQtyInvoiced();
                    if ($remaining < $qty) {
                        $qty = $remaining;
                    }
                }

                $invoiceItems[$shipmentItem->getOrderItemId()] = $qty;
            }

            /*
             * Capture online is selected, to make use of all the functions that it runs (payment
             * transactions etc). "qliro_skip_actual_capture" is set to avoid doing the capture
             * inside, since it was already done by the shipment
             */
            if ($order->canInvoice()) {
                $invoice = $order->prepareInvoice($invoiceItems);
                $invoice->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
                $payment->setTransactionId($qliroOrderManagementStatus->getPaymentTransactionId());
                $payment->setData(\Qliro\QliroOne\Model\Management::QLIRO_SKIP_ACTUAL_CAPTURE, 1);
                $invoice->register()->pay();
                $this->invoiceRepository->save($invoice);
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Order does not allow to capture')
                );
            }

            $formattedPrice = $order->getBaseCurrency()->formatTxt(
                $qliroOrderManagementStatus->getAmount()
            );

            $order->addStatusHistoryComment(__('Capture of %1 confirmed successful', $formattedPrice));

            $this->orderRepository->save($order);
        } catch (\Exception $exception) {
            $this->logManager->debug(
                $exception,
                [
                    'extra' => [
                        'qliro_order_id' => $qliroOrderManagementStatus->getOrderId(),
                        'shipment_id' => isset($shipment) ? $shipment->getId() : null,
                    ],
                ]
            );
            throw new TerminalException('Could not handle Shipment Success', null, $exception);
        }
    }

    /**
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @param \Qliro\QliroOne\Model\OrderManagementStatus $omStatus
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     */
    public function handleCancelled($qliroOrderManagementStatus, $omStatus)
    {
        $this->setOnHold($qliroOrderManagementStatus, $omStatus, 'Cancelled');
    }

    /**
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @param \Qliro\QliroOne\Model\OrderManagementStatus $omStatus
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     */
    public function handleError($qliroOrderManagementStatus, $omStatus)
    {
        $this->setOnHold($qliroOrderManagementStatus, $omStatus, 'Error');
    }

    /**
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @param \Qliro\QliroOne\Model\OrderManagementStatus $omStatus
     */
    public function handleInProcess($qliroOrderManagementStatus, $omStatus)
    {
        // Nothing to do
    }

    /**
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @param \Qliro\QliroOne\Model\OrderManagementStatus $omStatus
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     */
    public function handleOnHold($qliroOrderManagementStatus, $omStatus)
    {
        $this->setOnHold($qliroOrderManagementStatus, $omStatus, 'OnHold');
    }

    /**
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @param \Qliro\QliroOne\Model\OrderManagementStatus $omStatus
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     */
    public function handleUserInteraction($qliroOrderManagementStatus, $omStatus)
    {
        $this->setOnHold($qliroOrderManagementStatus, $omStatus, 'UserInteraction');
    }

    /**
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @param \Qliro\QliroOne\Model\OrderManagementStatus $omStatus
     */
    public function handleCreated($qliroOrderManagementStatus, $omStatus)
    {
        // Nothing to do
    }

    /**
     * @param \Qliro\QliroOne\Model\OrderManagementStatus $omStatus
     * @return \Magento\Sales\Model\Order\Shipment $shipment
     */
    private function getShipment($omStatus)
    {
        $shipment = $this->shipmentRepository->get($omStatus->getRecordId());

        return $shipment;
    }

    /**
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @param \Qliro\QliroOne\Model\OrderManagementStatus $omStatus
     * @param string $contextMessage
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     */
    private function setOnHold($qliroOrderManagementStatus, $omStatus, $contextMessage)
    {
        try {
            $shipment = $this->getShipment($omStatus);
            $order = $shipment->getOrder();
            $order->hold();

            $order->addStatusHistoryComment(
                __('Order set on hold because Qliro One reported an error with the capture: %1', $contextMessage)
            );

            $this->orderRepository->save($order);
        } catch (\Exception $exception) {
            $this->logManager->critical(
                $exception,
                [
                    'extra' => [
                        'qliro_order_id' => $qliroOrderManagementStatus->getOrderId(),
                    ],
                ]
            );

            throw new TerminalException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
