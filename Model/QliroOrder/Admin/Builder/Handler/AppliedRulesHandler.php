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
 * Applied Rules Handler class for order items builder
 */
class AppliedRulesHandler implements OrderItemHandlerInterface
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
        if (!$order->getFirstCaptureFlag()) {
            return $orderItems;
        }
        $arrayAppliedRules = sprintf('DSC_%s', \str_replace(',', '_', $order->getAppliedRuleIds()));
        // @todo might not be the exact same amount as quote calculation: $quote->getSubtotalWithDiscount() - $quote->getSubtotal();
        $discountAmount = (float)$order->getDiscountAmount();

        $formattedAmount = $this->qliroHelper->formatPrice($discountAmount);

        if ($discountAmount) {
            /** @var \Qliro\QliroOne\Api\Data\QliroOrderItemInterface $qliroOrderItem */
            $qliroOrderItem = $this->qliroOrderItemFactory->create();

            $qliroOrderItem->setMerchantReference($arrayAppliedRules);
            $qliroOrderItem->setDescription($arrayAppliedRules);
            $qliroOrderItem->setType(QliroOrderItemInterface::TYPE_DISCOUNT);
            $qliroOrderItem->setQuantity(1);
            $qliroOrderItem->setPricePerItemIncVat($formattedAmount);
            $qliroOrderItem->setPricePerItemExVat($formattedAmount);

            $orderItems[] = $qliroOrderItem;
        }

        return $orderItems;
    }
}
