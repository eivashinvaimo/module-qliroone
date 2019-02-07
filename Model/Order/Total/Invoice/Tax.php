<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Order\Total\Invoice;

class Tax extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    /**
     * Collect tax totals
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        if ($invoice->getQlirooneFee() > 0) {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $invoice->getOrder();

            $feeAmountTax = $order->getQlirooneFeeTax();
            $feeBaseAmountTax = $order->getBaseQlirooneFeeTax();
            $invoice->setQlirooneFeeTax($feeAmountTax);
            $invoice->setBaseQlirooneFeeTax($feeBaseAmountTax);

            $invoice->setGrandTotal($invoice->getGrandTotal() + $feeAmountTax);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $feeBaseAmountTax);
            $invoice->setTaxAmount($invoice->getTaxAmount() + $feeAmountTax);
            $invoice->setBaseTaxAmount($invoice->getBaseTaxAmount() + $feeBaseAmountTax);
        }

        return $this;
    }
}
