<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Block\Adminhtml\Sales\Order\Invoice;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Qliro\QliroOne\Model\Fee;

class Totals extends Template
{
    /**
     * @var Fee
     */
    private $fee;

    /**
     * Totals constructor.
     *
     * @param Context $context
     * @param Fee $fee
     * @param array $data
     */
    public function __construct(
        Context $context,
        Fee $fee,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->fee = $fee;
    }

    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
        /** @var \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals $parent */
        $parent = $this->getParentBlock();

        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $parent->getInvoice();

        if (!$invoice->getQlirooneFee()) {
            return $this;
        }

        $fee = $this->fee->getFeeObject($invoice->getStoreId(), $invoice->getQlirooneFee());
        $parent->addTotalBefore($fee, 'sub_total');

        return $this;
    }
}