<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\OrderManagementStatus\Update\Handler;

use Qliro\QliroOne\Api\Admin\OrderManagementStatusUpdateHandlerInterface;
use Magento\Sales\Model\Order;
use Qliro\QliroOne\Model\Exception\TerminalException;

class Payment implements OrderManagementStatusUpdateHandlerInterface
{
    /**
     * @var \Magento\Sales\Api\OrderPaymentRepositoryInterface
     */
    private $paymentRepository;

    /**
     * @var \Magento\Sales\Api\TransactionRepositoryInterface
     */
    private $paymentTransactionRepository;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Qliro\QliroOne\Model\Logger\Manager
     */
    private $logManager;

    /**
     * Payment constructor.
     * @param \Magento\Sales\Api\OrderPaymentRepositoryInterface $paymentRepository
     * @param \Magento\Sales\Api\TransactionRepositoryInterface $paymentTransactionRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Qliro\QliroOne\Model\Logger\Manager $logManager
     */
    public function __construct(
        \Magento\Sales\Api\OrderPaymentRepositoryInterface $paymentRepository,
        \Magento\Sales\Api\TransactionRepositoryInterface $paymentTransactionRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Qliro\QliroOne\Model\Logger\Manager $logManager
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->paymentTransactionRepository = $paymentTransactionRepository;
        $this->orderRepository = $orderRepository;
        $this->logManager = $logManager;
    }

    /**
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @param \Qliro\QliroOne\Model\OrderManagementStatus $omStatus
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     */
    public function handleSuccess($qliroOrderManagementStatus, $omStatus)
    {
        $payment = $this->getPayment($omStatus);
        $order = $payment->getOrder();

        /*
         * Update Order
         */
        try {
            if ($order->getState() == Order::STATE_HOLDED) {
                $order->unhold();
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
                        'payment_id' => $payment->getId(),
                    ],
                ]
            );
            throw new TerminalException('Could not handle Invoice Success', null, $exception);
        }

        /*
         * Update Payment Transaction
         */
        try {
            /** @var \Magento\Sales\Model\Order\Payment\Transaction $paymentTransaction */
            $paymentTransaction = $this->getPaymentTransaction(
                $qliroOrderManagementStatus->getPaymentTransactionId(),
                $payment->getId(),
                $order->getId()
            );

            $paymentTransaction->setAdditionalInformation(
                'provider_result_description',
                $qliroOrderManagementStatus->getProviderResultDescription()
            );
            $paymentTransaction->setAdditionalInformation(
                'provider_result_code',
                $qliroOrderManagementStatus->getProviderResultCode()
            );
            $paymentTransaction->setAdditionalInformation(
                'provider_transaction_id',
                $qliroOrderManagementStatus->getProviderTransactionId()
            );
            $paymentTransaction->setAdditionalInformation(
                'payment_reference',
                $qliroOrderManagementStatus->getPaymentReference()
            );

            $this->paymentTransactionRepository->save($paymentTransaction);
        } catch (\Exception $exception) {
            $this->logManager->debug(
                $exception,
                [
                    'extra' => [
                        'qliro_order_id' => $qliroOrderManagementStatus->getOrderId(),
                        'payment_id' => $payment->getId(),
                    ],
                ]
            );
            // Silent, since this code is not required, just nice to haves
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
     * @return \Magento\Sales\Model\Order\Payment $payment
     */
    private function getPayment($omStatus)
    {
        $payment = $this->paymentRepository->get($omStatus->getRecordId());

        return $payment;
    }

    /**
     * Get payment transaction with the same transaction number as was part of this notification
     *
     * @param int $transactionId
     * @param int $paymentId
     * @param int $orderId
     * @return \Magento\Sales\Model\Order\Payment $payment
     */
    private function getPaymentTransaction($transactionId, $paymentId, $orderId)
    {
        $paymentTransaction = $this->paymentTransactionRepository->getByTransactionId(
            $transactionId,
            $paymentId,
            $orderId
        );

        return $paymentTransaction;
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
            $payment = $this->getPayment($omStatus);
            $order = $payment->getOrder();
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
