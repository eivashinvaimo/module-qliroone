<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Order\Total\Creditmemo;

class Tax extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * Collect tax totals
     *
     * @param   \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return  $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        if ($creditmemo->getQlirooneFee() > 0) {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $creditmemo->getOrder();

            $feeAmountTax = $order->getQlirooneFeeTax();
            $feeBaseAmountTax = $order->getBaseQlirooneFeeTax();
            $creditmemo->setQlirooneFeeTax($feeAmountTax);
            $creditmemo->setBaseQlirooneFeeTax($feeBaseAmountTax);

// Logic tells me that this should be in, but adding this, will cause it to double the taxes...
//            $creditmemo->setTaxAmount($creditmemo->getTaxAmount() + $feeAmountTax);
//            $creditmemo->setBaseTaxAmount($creditmemo->getBaseTaxAmount() + $feeBaseAmountTax);
        }

        return $this;
    }
}
