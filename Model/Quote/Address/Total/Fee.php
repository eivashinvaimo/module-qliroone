<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Quote\Address\Total;

use Magento\Checkout\Model\Session;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\QuoteValidator;
use Qliro\QliroOne\Model\Config;
use Magento\Quote\Model\Quote\Address;

class Fee extends AbstractTotal
{
    /**
     * @var string
     */
    protected $_code = Config::TOTALS_FEE_CODE;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * Collect grand total address amount
     */
    private $quoteValidator = null;
    
    /**
     * @var \Qliro\QliroOne\Model\Fee
     */
    private $fee;

    /**
     * Fee constructor.
     *
     * @param QuoteValidator $quoteValidator
     * @param Session $checkoutSession
     * @param PaymentInterface $payment
     * @param \Qliro\QliroOne\Model\Fee $fee
     */
    public function __construct(
        QuoteValidator $quoteValidator,
        Session $checkoutSession,
        PaymentInterface $payment,
        \Qliro\QliroOne\Model\Fee $fee
    ) {
        $this->quoteValidator = $quoteValidator;
        $this->checkoutSession = $checkoutSession;
        $this->fee = $fee;
    }

    /**
     * Collect totals
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $total->setQlirooneFee(0);
        $total->setBaseQlirooneFee(0);
        $total->setQlirooneFeeExclTax(0);
        $total->setBaseQlirooneFeeExclTax(0);

        $total->setTotalAmount(Config::TOTALS_FEE_CODE, 0);
        $total->setBaseTotalAmount(Config::TOTALS_BASE_FEE_CODE, 0);

        if ($quote->isVirtual()) {
            if ($shippingAssignment->getShipping()->getAddress()->getAddressType() != Address::TYPE_BILLING) {
                return $this;
            }
        } else {
            if (!count($shippingAssignment->getItems())) {
                return $this;
            }
        }

        $fee = $this->fee->getQlirooneFeeInclTax($quote);
        $baseFee = $this->fee->getBaseQlirooneFeeInclTax($quote);

        $feeExclTax = $this->fee->getQlirooneFeeExclTax($quote);
        $baseFeeExclTax = $this->fee->getBaseQlirooneFeeExclTax($quote);

        $total->setQlirooneFee($fee);
        $total->setBaseQlirooneFee($baseFee);
        $total->setQlirooneFeeExclTax($feeExclTax);
        $total->setBaseQlirooneFeeExclTax($baseFeeExclTax);

        $total->setTotalAmount(Config::TOTALS_FEE_CODE, $feeExclTax);
        $total->setBaseTotalAmount(Config::TOTALS_BASE_FEE_CODE, $baseFeeExclTax);

        return $this;
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(Quote $quote, Total $total)
    {
        return $this->fee->getFeeArray($quote, $total->getQlirooneFee());
    }

    /**
     * Get Subtotal label. Doubt this is ever used...
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Payment Fee');
    }
}