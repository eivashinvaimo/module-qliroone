<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Admin\Builder;

use Magento\Sales\Model\Order;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Tax\Model\Calculation as TaxCalculation;
use Qliro\QliroOne\Api\Admin\Builder\OrderItemHandlerInterface;
use Qliro\QliroOne\Api\Data\QliroOrderItemInterfaceFactory;
use Qliro\QliroOne\Helper\Data as QliroHelper;
use Qliro\QliroOne\Model\Product\Type\OrderSourceProvider;
use Qliro\QliroOne\Model\Product\Type\TypePoolHandler;

/**
 * QliroOne Admin Order items builder class
 */
class InvoiceOrderItemsBuilder
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
     * @var \Magento\Tax\Helper\Data
     */
    private $taxHelper;

    /**
     * @var \Qliro\QliroOne\Model\Product\Type\TypePoolHandler
     */
    private $typeResolver;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderItemInterfaceFactory
     */
    private $qliroOrderItemFactory;

    /**
     * @var \Magento\Tax\Model\Calculation
     */
    private $taxCalculation;

    /**
     * @var \Qliro\QliroOne\Helper\Data
     */
    private $qliroHelper;

    /**
     * @var \Qliro\QliroOne\Api\Admin\Builder\OrderItemHandlerInterface[]
     */
    private $handlers = [];

    /**
     * @var OrderSourceProvider
     */
    private $orderSourceProvider;

    /**
     * Inject dependencies
     *
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Qliro\QliroOne\Model\Product\Type\TypePoolHandler $typeResolver
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterfaceFactory $qliroOrderItemFactory
     * @param \Qliro\QliroOne\Helper\Data $qliroHelper
     * @param OrderSourceProvider $orderSourceProvider
     * @param \Qliro\QliroOne\Api\Admin\Builder\OrderItemHandlerInterface[] $handlers
     */
    public function __construct(
        TaxHelper $taxHelper,
        TaxCalculation $taxCalculation,
        TypePoolHandler $typeResolver,
        QliroOrderItemInterfaceFactory $qliroOrderItemFactory,
        QliroHelper $qliroHelper,
        OrderSourceProvider $orderSourceProvider,
        $handlers = []
    ) {
        $this->taxHelper = $taxHelper;
        $this->typeResolver = $typeResolver;
        $this->qliroOrderItemFactory = $qliroOrderItemFactory;
        $this->taxCalculation = $taxCalculation;
        $this->qliroHelper = $qliroHelper;
        $this->handlers = $handlers;
        $this->orderSourceProvider = $orderSourceProvider;
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
     * Create an array of containers
     *
     * @return \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[]
     */
    public function create()
    {
        if (empty($this->order)) {
            throw new \LogicException('Order entity is not set.');
        }

        $result = [];

        /*
         * Contains the order item id of each valid configurable about to be invoiced in this format:
         * $configurableProducts['order item id of configurable'] = quantity about to be captured
         */
        $configurableProducts = [];

        /** @var \Magento\Sales\Model\Order\Invoice\Item $invoiceItem */
        foreach ($this->invoice->getAllItems() as $invoiceItem) {
            /** @var \Magento\Sales\Model\Order\Item $orderItem */
            $orderItem = $this->order->getItemById($invoiceItem->getOrderItemId());
            $invoiceQty = (int)$invoiceItem->getQty();

            if ($orderItem->getProductType() == 'configurable') {
                $configurableProducts[$orderItem->getId()] = $invoiceQty;
            }

            if ($orderItem->getParentItemId()) {
                if (!isset($configurableProducts[$orderItem->getParentItemId()])) {
                    continue;
                }
                $invoiceQty = $configurableProducts[$orderItem->getParentItemId()];
            }

            if (!$invoiceQty) {
                continue;
            }

            $qliroOrderItem = $this->typeResolver->resolveQliroOrderItem(
                $this->orderSourceProvider->generateSourceItem($orderItem),
                $this->orderSourceProvider
            );

            if ($qliroOrderItem) {
                $qliroOrderItem->setQuantity($invoiceQty);
                $result[] = $qliroOrderItem;
            }
        }

        if ($this->isFirstInvoice()) {
            $this->order->setFirstCaptureFlag(true);
        }

        foreach ($this->handlers as $handler) {
            if ($handler instanceof OrderItemHandlerInterface) {
                $result = $handler->handle($result, $this->order);
            }
        }

        $this->payment = null;
        $this->order = null;
        $this->invoice = null;
        $this->orderSourceProvider->setOrder($this->order);

        return $result;
    }

    /**
     * @return bool
     */
    private function isFirstInvoice()
    {
        $invoiceCollection = $this->order->getInvoiceCollection();
        foreach ($invoiceCollection as $invoice) {
            if ($invoice->getId() == $this->invoice->getId()) {
                continue;
            }
            return false;
        }
        return true;
    }
}

