<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Converter;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Qliro\QliroOne\Api\Data\QliroOrderItemInterface;
use Qliro\QliroOne\Model\Product\Type\QuoteSourceProvider;
use Qliro\QliroOne\Model\Product\Type\TypePoolHandler;
use Qliro\QliroOne\Model\Fee;
use Qliro\QliroOne\Model\QliroOrder\Admin\Builder\Handler\InvoiceFeeHandler;

/**
 * QliroOne Order Items Converter class
 */
class OrderItemsConverter
{
    /**
     * @var \Qliro\QliroOne\Model\Product\Type\TypePoolHandler
     */
    private $typePoolHandler;

    /**
     * @var \Qliro\QliroOne\Model\Fee
     */
    private $fee;

    /**
     * @var \Qliro\QliroOne\Model\Product\Type\QuoteSourceProvider
     */
    private $quoteSourceProvider;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Model\Product\Type\TypePoolHandler $typePoolHandler
     * @param \Qliro\QliroOne\Model\Fee $fee
     * @param \Qliro\QliroOne\Model\Product\Type\QuoteSourceProvider $quoteSourceProvider
     */
    public function __construct(
        TypePoolHandler $typePoolHandler,
        Fee $fee,
        QuoteSourceProvider $quoteSourceProvider
    ) {
        $this->typePoolHandler = $typePoolHandler;
        $this->fee = $fee;
        $this->quoteSourceProvider = $quoteSourceProvider;
    }

    /**
     * Convert QliroOne order items into relevant quote items
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[] $qliroOrderItems
     * @param \Magento\Quote\Model\Quote $quote
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function convert($qliroOrderItems, Quote $quote)
    {
        $feeAmount = 0;
        $shippingCode = null;
        $this->quoteSourceProvider->setQuote($quote);

        if (!$quote->isVirtual()) {
            $shippingCode = $quote->getShippingAddress()->getShippingMethod();
        }

        foreach ($qliroOrderItems as $orderItem) {
            switch ($orderItem->getType()) {
                case QliroOrderItemInterface::TYPE_PRODUCT:
                    $this->typePoolHandler->resolveQuoteItem($orderItem, $this->quoteSourceProvider);
                    break;

                case QliroOrderItemInterface::TYPE_SHIPPING:
                    $shippingCode = $orderItem->getMerchantReference();
                    break;

                case QliroOrderItemInterface::TYPE_DISCOUNT:
                    // Not doing it now
                    break;

                case QliroOrderItemInterface::TYPE_FEE:
                    $feeAmount += $orderItem->getPricePerItemIncVat();
                    $quote->getPayment()->setAdditionalInformation(
                        InvoiceFeeHandler::MERCHANT_REFERENCE_CODE_FIELD,
                        $orderItem->getMerchantReference()
                    );
                    $quote->getPayment()->setAdditionalInformation(
                        InvoiceFeeHandler::MERCHANT_REFERENCE_DESCRIPTION_FIELD,
                        $orderItem->getDescription()
                    );
                    break;
            }
        }

        if (!$quote->isVirtual() && $shippingCode) {
            $this->applyShippingMethod($shippingCode, $quote);
        }

        $this->fee->setQlirooneFeeInclTax($quote, $feeAmount);
    }

    /**
     * @param string $code
     * @param \Magento\Quote\Model\Quote $quote
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function applyShippingMethod($code, Quote $quote)
    {
        if (empty($code)) {
            throw new LocalizedException(__('Invalid shipping method.'));
        }

        $rate = $quote->getShippingAddress()->getShippingRateByCode($code);

        if (!$rate) {
            throw new LocalizedException(__('Invalid shipping method.'));
        }

        if ($quote->isMultipleShippingAddresses()) {
            throw new LocalizedException(
                __('There are more than one shipping addresses.')
            );
        }

        $extensionAttributes = $quote->getExtensionAttributes();

        if ($extensionAttributes !== null) {
            $shippingAssignments = $quote->getExtensionAttributes()->getShippingAssignments();

            foreach ($shippingAssignments as $assignment) {
                $assignment->getShipping()->setMethod($code);
            }
        }

        $quote->getShippingAddress()->setShippingMethod($code);
    }
}
