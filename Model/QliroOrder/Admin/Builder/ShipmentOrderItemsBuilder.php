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
class ShipmentOrderItemsBuilder
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
     * @var \Magento\Sales\Model\Order\Shipment
     */
    private $shipment;

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
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     */
    public function setShipment($shipment)
    {
        $this->shipment = $shipment;

        /** @var \Magento\Sales\Model\Order $order */
        $this->order = $this->shipment->getOrder();

        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $this->payment = $this->order->getPayment();
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
         * Contains the order item id of each valid configurable about to be shipped in this format:
         * $configurableProducts['order item id of configurable'] = quantity about to be captured
         */
        $configurableProducts = [];

        /** @var \Magento\Sales\Model\Order\Shipment\Item $shipmentItem */
        foreach ($this->shipment->getItemsCollection() as $shipmentItem) {
            /** @var \Magento\Sales\Model\Order\Item $orderItem */
            $orderItem = $this->order->getItemById($shipmentItem->getOrderItemId());
            $shipmentQty = (int)$shipmentItem->getQty();

            if ($orderItem->getProductType() == 'configurable') {
                /**
                 * This calculates how many items to ship, in case invoice was created Before shipment
                 */
                if ($orderItem->getQtyInvoiced() > 0) {
                    $remaining = $orderItem->getQtyOrdered() - $orderItem->getQtyInvoiced();
                    if ($remaining < $shipmentQty) {
                        $shipmentQty = $remaining;
                    }
                }
                $configurableProducts[$orderItem->getId()] = $shipmentQty;
            }

            if ($orderItem->getParentItemId()) {
                if (!isset($configurableProducts[$orderItem->getParentItemId()])) {
                    continue;
                }
                $shipmentQty = $configurableProducts[$orderItem->getParentItemId()];
            }

            if (!$shipmentQty) {
                continue;
            }

            $qliroOrderItem = $this->typeResolver->resolveQliroOrderItem(
                $this->orderSourceProvider->generateSourceItem($orderItem),
                $this->orderSourceProvider
            );

            if ($qliroOrderItem) {
                $qliroOrderItem->setQuantity($shipmentQty);
                $result[] = $qliroOrderItem;
            }
        }

        if ($this->isFirstShipment()) {
            $this->order->setFirstCaptureFlag(true);
        }

        foreach ($this->handlers as $handler) {
            if ($handler instanceof OrderItemHandlerInterface) {
                $result = $handler->handle($result, $this->order);
            }
        }

        $this->payment = null;
        $this->order = null;
        $this->shipment = null;
        $this->orderSourceProvider->setOrder($this->order);

        return $result;
    }

    /**
     * @return bool
     */
    private function isFirstShipment()
    {
        $invoiceCollection = $this->order->getInvoiceCollection();
        foreach ($invoiceCollection as $invoice) {
            return false;
        }
        $shipmentCollection = $this->order->getShipmentsCollection();
        foreach ($shipmentCollection as $shipment) {
            if ($shipment->getId() == $this->shipment->getId()) {
                continue;
            }
            return false;
        }
        return true;
    }
}

