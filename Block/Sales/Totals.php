<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Block\Sales;

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
     * Check if we nedd display full tax total info
     *
     * @return bool
     */
    public function displayFullSummary()
    {
        return true;
    }

    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
        /** @var \Magento\Sales\Block\Order\Totals $parent */
        $parent = $this->getParentBlock();

        /** @var \Magento\Sales\Model\Order _order */
        $order = $parent->getOrder();

        if (!$order->getQlirooneFee()) {
            return $this;
        }

        $fee = $this->fee->getFeeObject($order->getStoreId(), $order->getQlirooneFee());
        $parent->addTotalBefore($fee, 'sub_total');

        return $this;
    }
}