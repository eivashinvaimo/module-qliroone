<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Qliro\QliroOne\Model\Quote\Address\Total;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote\Address;
use Qliro\QliroOne\Model\Config;
use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;

class Tax extends AbstractTotal
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var \Qliro\QliroOne\Model\Fee
     */
    private $fee;

    /**
     * @param Config $config
     * @param \Qliro\QliroOne\Model\Fee $fee
     */
    public function __construct(
        Config $config,
        \Qliro\QliroOne\Model\Fee $fee
    ) {
        $this->setCode('tax_qliroone_fee');
        $this->config = $config;
        $this->fee = $fee;
    }

    /**
     * Collect Payment Fee and add it to tax calculation
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
        $total->setQlirooneFeeTax(0);
        $total->setBaseQlirooneFeeTax(0);

        if ($quote->isVirtual()) {
            if ($shippingAssignment->getShipping()->getAddress()->getAddressType() != Address::TYPE_BILLING) {
                return $this;
            }
        } else {
            if ($shippingAssignment->getShipping()->getAddress()->getAddressType() != Address::TYPE_SHIPPING) {
                return $this;
            }
        }

        $productTaxClassId = $this->config->getFeeTaxClass($quote->getStore());

        $address = $shippingAssignment->getShipping()->getAddress();

        $fee = $total->getQlirooneFee();
        $baseFee = $total->getBaseQlirooneFee();

        $feeExclTax = $total->getQlirooneFeeExclTax();
        $baseFeeExclTax = $total->getBaseQlirooneFeeExclTax();

        $feeAmountTax = $fee - $feeExclTax;
        $feeBaseAmountTax = $baseFee - $baseFeeExclTax;

        $associatedTaxables = $address->getAssociatedTaxables();
        if (!$associatedTaxables) {
            $associatedTaxables = [];
        }

        $associatedTaxables[] = [
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_TYPE => Config::TOTALS_FEE_CODE,
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_CODE => Config::TOTALS_FEE_CODE,
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_UNIT_PRICE => $fee,
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_BASE_UNIT_PRICE => $baseFee,
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_QUANTITY => 1,
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_TAX_CLASS_ID => $productTaxClassId,
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_PRICE_INCLUDES_TAX => true,
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_ASSOCIATION_ITEM_CODE
            => CommonTaxCollector::ASSOCIATION_ITEM_CODE_FOR_QUOTE,
        ];

        $address->setAssociatedTaxables($associatedTaxables);

        $total->setQlirooneFeeTax($feeAmountTax);
        $total->setBaseQlirooneFeeTax($feeBaseAmountTax);

        return $this;
    }

    /**
     * Assign Payment Fee tax totals and labels to address object
     *
     * @param Quote $quote
     * @param Total $total
     * @return null
     */
    public function fetch(Quote $quote, Total $total)
    {
        return null;
    }
}
