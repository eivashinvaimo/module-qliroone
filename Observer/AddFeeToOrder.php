<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddFeeToOrder implements ObserverInterface
{
    /**
     * Set payment fee to order
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getQuote();

        /** @var \Magento\Quote\Model\Quote\Address $address */
        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }

        $feeAmount = $address->getQlirooneFee();
        $baseQlirooneFee = $address->getBaseQlirooneFee();
        if (!$feeAmount || !$baseQlirooneFee) {
            return $this;
        }
        $feeAmountTax = $address->getQlirooneFeeTax();
        $baseQlirooneFeeTax = $address->getBaseQlirooneFeeTax();

        //Set fee data to order
        $order = $observer->getOrder();
        $order->setQlirooneFee($feeAmount);
        $order->setQlirooneFeeTax($feeAmountTax);
        $order->setBaseQlirooneFee($baseQlirooneFee);
        $order->setBaseQlirooneFeeTax($baseQlirooneFeeTax);

        return $this;
    }
}