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
class AddInvoiceToCapture implements ObserverInterface
{
    /**
     * @param Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $payment = $observer->getPayment();

        if ($payment->getMethod() == QliroOne::PAYMENT_METHOD_CHECKOUT_CODE) {
            $payment->setInvoice($observer->getInvoice());
        }
    }
}
