<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Order\Total\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class Fee extends AbstractTotal
{
    /**
     * Collect totals
     *
     * @param Creditmemo $creditmemo
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $creditmemo->getOrder();
        if ($order->getQlirooneFeeInvoiced() > 0) {
            $feeAmount = $order->getQlirooneFeeInvoiced() - $order->getQlirooneFeeRefunded();
            if ($feeAmount > 0) {
                $basefeeAmount = $order->getBaseQlirooneFeeInvoiced() - $order->getBaseQlirooneFeeRefunded();

                $feeAmountTax = $order->getQlirooneFeeTax();
                $basefeeAmountTax = $order->getBaseQlirooneFeeTax();
                $creditmemo->setQlirooneFee($feeAmount);
                $creditmemo->setBaseQlirooneFee($basefeeAmount);

                $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $feeAmount - $feeAmountTax);
                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $basefeeAmount - $basefeeAmountTax);
                $order->setQlirooneFeeRefunded($order->getQlirooneFeeRefunded() + $feeAmount);
                $order->setBaseQlirooneFeeRefunded($order->getBaseQlirooneFeeRefunded() + $basefeeAmount);
            }
        }

        return $this;
    }
}