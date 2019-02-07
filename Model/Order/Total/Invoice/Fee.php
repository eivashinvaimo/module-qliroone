<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Order\Total\Invoice;

use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class Fee extends AbstractTotal
{
    /**
     * Collect totals
     *
     * @param Invoice $invoice
     * @return $this
     */
    public function collect(Invoice $invoice)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $invoice->getOrder();
        if (!$order->getQlirooneFeeInvoiced()) {
            $feeAmount = $order->getQlirooneFee();
            $feeAmountTax = $order->getQlirooneFeeTax();
            $basefeeAmount = $order->getBaseQlirooneFee();
            $basefeeAmountTax = $order->getBaseQlirooneFeeTax();
            $invoice->setQlirooneFee($feeAmount);
            $invoice->setBaseQlirooneFee($basefeeAmount);

            $invoice->setGrandTotal($invoice->getGrandTotal() + $feeAmount - $feeAmountTax);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $basefeeAmount - $basefeeAmountTax);
            $order->setQlirooneFeeInvoiced($feeAmount);
            $order->setBaseQlirooneFeeInvoiced($basefeeAmount);
        }

        return $this;
    }
}