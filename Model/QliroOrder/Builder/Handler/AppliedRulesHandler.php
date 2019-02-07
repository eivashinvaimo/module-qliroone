<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Builder\Handler;

use Magento\Framework\Event\ManagerInterface;
use Qliro\QliroOne\Api\Builder\OrderItemHandlerInterface;
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
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterfaceFactory $qliroOrderItemFactory
     * @param \Qliro\QliroOne\Helper\Data $qliroHelper
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        QliroOrderItemInterfaceFactory $qliroOrderItemFactory,
        QliroHelper $qliroHelper,
        ManagerInterface $eventManager
    ) {

        $this->qliroOrderItemFactory = $qliroOrderItemFactory;
        $this->qliroHelper = $qliroHelper;
        $this->eventManager = $eventManager;
    }

    /**
     * Handle specific type of order items and add them to the QliroOne order items list
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[] $orderItems
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[]
     */
    public function handle($orderItems, $quote)
    {
        $arrayAppliedRules = sprintf('DSC_%s', \str_replace(',', '_', $quote->getAppliedRuleIds()));
        $discountAmount = $quote->getSubtotalWithDiscount() - $quote->getSubtotal();
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

            // Note that this event dispatch must be done for every implemented Handler
            $this->eventManager->dispatch(
                'qliroone_order_item_build_after',
                [
                    'quote' => $quote,
                    'container' => $qliroOrderItem,
                ]
            );

            if ($qliroOrderItem->getMerchantReference()) {
                $orderItems[] = $qliroOrderItem;
            }
        }

        return $orderItems;
    }
}
