<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Admin\Builder\Handler;

use Qliro\QliroOne\Api\Admin\Builder\OrderItemHandlerInterface;
use Qliro\QliroOne\Api\Data\QliroOrderItemInterface;
use Qliro\QliroOne\Api\Data\QliroOrderItemInterfaceFactory;
use Qliro\QliroOne\Helper\Data as QliroHelper;

/**
 * Shipping Fee Handler class for order items builder
 */
class ShippingFeeHandler implements OrderItemHandlerInterface
{
    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderItemInterfaceFactory
     */
    private $qliroOrderItemFactory;

    /**
     * @var \Qliro\QliroOne\Helper\Data
     */
    private $qliroHelper;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterfaceFactory $qliroOrderItemFactory
     * @param \Qliro\QliroOne\Helper\Data $qliroHelper
     */
    public function __construct(
        QliroOrderItemInterfaceFactory $qliroOrderItemFactory,
        QliroHelper $qliroHelper
    ) {

        $this->qliroOrderItemFactory = $qliroOrderItemFactory;
        $this->qliroHelper = $qliroHelper;
    }

    /**
     * Handle specific type of order items and add them to the QliroOne order items list
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[] $orderItems
     * @param \Magento\Sales\Model\Order $order
     * @return \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[]
     */
    public function handle($orderItems, $order)
    {
        // @todo Handle invoiced and refunded shipping
        if (!$order->getFirstCaptureFlag()) {
            return $orderItems;
        }
        $merchantReference = $order->getShippingMethod();
        $inclTax = (float)$order->getShippingAmount() + $order->getShippingTaxAmount();
        $exclTax = (float)$order->getShippingAmount();

        $formattedInclAmount = $this->qliroHelper->formatPrice($inclTax);
        $formattedExclAmount = $this->qliroHelper->formatPrice($exclTax);

        if ($formattedInclAmount) {
            /** @var \Qliro\QliroOne\Api\Data\QliroOrderItemInterface $qliroOrderItem */
            $qliroOrderItem = $this->qliroOrderItemFactory->create();

            $qliroOrderItem->setMerchantReference($merchantReference);
            $qliroOrderItem->setDescription($merchantReference);
            $qliroOrderItem->setType(QliroOrderItemInterface::TYPE_SHIPPING);
            $qliroOrderItem->setQuantity(1);
            $qliroOrderItem->setPricePerItemIncVat($formattedInclAmount);
            $qliroOrderItem->setPricePerItemExVat($formattedExclAmount);

            $orderItems[] = $qliroOrderItem;
        }

        return $orderItems;
    }
}
