<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Admin\Builder;

use Magento\Framework\Exception\NoSuchEntityException;
use Qliro\QliroOne\Api\Data\AdminMarkItemsAsShippedRequestInterfaceFactory;
use Qliro\QliroOne\Api\LinkRepositoryInterface;
use Qliro\QliroOne\Model\Logger\Manager as LogManager;
use Qliro\QliroOne\Model\Config;

/**
 * Mark Items As Shipped Request Builder class
 */
class InvoiceMarkItemsAsShippedRequestBuilder
{
    /**
     * @var \Magento\Sales\Model\Order\Payment
     */
    private $payment;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $order;

    /**
     * @var \Magento\Sales\Model\Order\Invoice
     */
    private $invoice;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var \Qliro\QliroOne\Api\Data\AdminMarkItemsAsShippedRequestInterfaceFactory
     */
    private $requestFactory;

    /**
     * @var \Qliro\QliroOne\Api\LinkRepositoryInterface
     */
    private $linkRepository;

    /**
     * @var \Qliro\QliroOne\Model\Logger\Manager
     */
    private $logManager;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Admin\Builder\InvoiceOrderItemsBuilder
     */
    private $orderItemsBuilder;

    /**
     * @var \Qliro\QliroOne\Model\Config
     */
    private $qliroConfig;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Api\Data\AdminMarkItemsAsShippedRequestInterfaceFactory $requestFactory
     * @param \Qliro\QliroOne\Api\LinkRepositoryInterface $linkRepository
     * @param \Qliro\QliroOne\Model\Logger\Manager $logManager
     * @param \Qliro\QliroOne\Model\QliroOrder\Admin\Builder\InvoiceOrderItemsBuilder $orderItemsBuilder
     * @param \Qliro\QliroOne\Model\Config $qliroConfig
     */
    public function __construct(
        AdminMarkItemsAsShippedRequestInterfaceFactory $requestFactory,
        LinkRepositoryInterface $linkRepository,
        LogManager $logManager,
        InvoiceOrderItemsBuilder $orderItemsBuilder,
        Config $qliroConfig
    ) {
        $this->requestFactory = $requestFactory;
        $this->linkRepository = $linkRepository;
        $this->logManager = $logManager;
        $this->orderItemsBuilder = $orderItemsBuilder;
        $this->qliroConfig = $qliroConfig;
    }

    /**
     * @param \Magento\Sales\Model\Order\Payment $payment
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;

        /** @var \Magento\Sales\Model\Order $order */
        $this->order = $this->payment->getOrder();

        /** @var  \Magento\Sales\Model\Order\Invoice $invoice */
        $this->invoice = $this->payment->getInvoice();
    }

    /**
     * Amount from Magento Capture call, is not actually used, but could be used for double checking...
     *
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return \Qliro\QliroOne\Api\Data\AdminMarkItemsAsShippedRequestInterface
     */
    public function create()
    {
        if (empty($this->order)) {
            throw new \LogicException('Order entity is not set.');
        }

        $request = $this->prepareRequest();

        $this->payment = null;
        $this->order = null;
        $this->invoice = null;

        return $request;
    }

    /**
     * Prepare a new request
     *
     * @return \Qliro\QliroOne\Api\Data\AdminMarkItemsAsShippedRequestInterface
     */
    private function prepareRequest()
    {
        /** @var \Qliro\QliroOne\Api\Data\AdminMarkItemsAsShippedRequestInterface $request */
        $request = $this->requestFactory->create();

        try {
            $link = $this->linkRepository->getByOrderId($this->order->getId());

            $request->setMerchantApiKey($this->qliroConfig->getMerchantApiKey());
            $request->setCurrency($this->order->getOrderCurrencyCode());
            $request->setOrderId($link->getQliroOrderId());

            $this->orderItemsBuilder->setPayment($this->payment);
            $orderItems = $this->orderItemsBuilder->create();

            $request->setOrderItems($orderItems);

        } catch (NoSuchEntityException $exception) {
            $this->logManager->debug(
                $exception,
                [
                    'extra' => [
                        'link_id' => $link->getId(),
                        'quote_id' => $link->getQuoteId(),
                        'qliro_order_id' => $link->getQliroOrderId(),
                    ],
                ]
            );

        }

        return $request;
    }
}
