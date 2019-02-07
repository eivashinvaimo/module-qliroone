<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Qliro\QliroOne\Model\Method\QliroOne;

/**
 * As capture event doesn't contain the invoice (it's meant to capture amount only), this observer
 * adds the invoice to the payment object for later retrieval
 */
class CaptureOnShipment implements ObserverInterface
{
    /**
     * @var \Qliro\QliroOne\Model\Management
     */
    private $management;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Model\Management $management
     */
    public function __construct(
        \Qliro\QliroOne\Model\Management $management
    ) {
        $this->management = $management;
    }

    /**
     * @param Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $observer->getEvent()->getShipment();

        $order = $shipment->getOrder();
        $payment = $order->getPayment();

        if ($payment->getMethod() == QliroOne::PAYMENT_METHOD_CHECKOUT_CODE) {
            $this->management->captureByShipment($shipment);
        }
    }
}
