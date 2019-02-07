<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model;

use Magento\Checkout\Model\Session;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Tax\Api\Data\QuoteDetailsInterfaceFactory;
use Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory;
use Magento\Tax\Api\Data\TaxClassKeyInterfaceFactory;
use Magento\Tax\Api\TaxCalculationInterface;
use Magento\Tax\Api\Data\TaxClassKeyInterface;

class Fee
{
    /**
     * @var array
     */
    private $methodsWithFee = [];

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var CatalogHelper
     */
    private $catalogHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var TaxClassKeyInterfaceFactory
     */
    private $taxClassKeyFactory;

    /**
     * @var QuoteDetailsInterfaceFactory
     */
    private $quoteDetailsFactory;

    /**
     * @var QuoteDetailsItemInterfaceFactory
     */
    private $quoteDetailsItemFactory;

    /**
     * @var TaxCalculationInterface
     */
    private $taxCalculation;

    /**
     * @var AddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * @var RegionInterfaceFactory
     */
    private $regionFactory;

    /**
     * @var GroupRepositoryInterface
     */
    private $customerGroupRepository;

    /**
     * Fee constructor.
     *
     * @param Config $config
     * @param Session $checkoutSession
     * @param PriceCurrencyInterface $priceCurrency
     * @param CatalogHelper $catalogHelper
     * @param StoreManagerInterface $storeManager
     * @param CustomerSession $customerSession
     * @param TaxClassKeyInterfaceFactory $taxClassKeyFactory
     * @param QuoteDetailsInterfaceFactory $quoteDetailsFactory
     * @param QuoteDetailsItemInterfaceFactory $quoteDetailsItemFactory
     * @param TaxCalculationInterface $taxCalculation
     * @param AddressInterfaceFactory $addressFactory
     * @param RegionInterfaceFactory $regionFactory
     * @param GroupRepositoryInterface $customerGroupRepository
     */
    public function __construct(
        Config $config,
        Session $checkoutSession,
        PriceCurrencyInterface $priceCurrency,
        CatalogHelper $catalogHelper,
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        TaxClassKeyInterfaceFactory $taxClassKeyFactory,
        QuoteDetailsInterfaceFactory $quoteDetailsFactory,
        QuoteDetailsItemInterfaceFactory $quoteDetailsItemFactory,
        TaxCalculationInterface $taxCalculation,
        AddressInterfaceFactory $addressFactory,
        RegionInterfaceFactory $regionFactory,
        GroupRepositoryInterface $customerGroupRepository
    ) {
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
        $this->priceCurrency = $priceCurrency;
        $this->catalogHelper = $catalogHelper;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->taxClassKeyFactory = $taxClassKeyFactory;
        $this->quoteDetailsFactory = $quoteDetailsFactory;
        $this->quoteDetailsItemFactory = $quoteDetailsItemFactory;
        $this->taxCalculation = $taxCalculation;
        $this->addressFactory = $addressFactory;
        $this->regionFactory = $regionFactory;
        $this->customerGroupRepository = $customerGroupRepository;
    }

    /**
     * Sets the fee including Tax, quote should be recalculated after this, to update all remaining fields
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param float $fee
     */
    public function setQlirooneFeeInclTax(\Magento\Quote\Model\Quote $quote, $fee)
    {
        if ($quote->isVirtual()) {
            $quote->getBillingAddress()->setQlirooneFee($fee);
        } else {
            $quote->getShippingAddress()->setQlirooneFee($fee);
        }
    }

    /**
     * Returns the amount of the fee, if defined. It can be fixed or a percent of the order sum
     * This function must not depend on display settings
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return float|int
     */
    public function getQlirooneFeeInclTax(\Magento\Quote\Model\Quote $quote)
    {
        if ($quote->isVirtual()) {
            $fee = $quote->getBillingAddress()->getQlirooneFee();
        } else {
            $fee = $quote->getShippingAddress()->getQlirooneFee();
        }
        $price = $this->getCalcTaxPrice($quote, $fee, 1);

        return $price;
    }

    /**
     * Return Fee exluding tax
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return float|int
     */
    public function getQlirooneFeeExclTax(\Magento\Quote\Model\Quote $quote)
    {
        $price = $this->getQlirooneFeeInclTax($quote);
        $price = $this->getCalcTaxPrice($quote, $price, 0);

        return $price;
    }

    /**
     * @todo Improvement. Proper currency conversion to handle display currencies
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return float|int
     */
    public function getBaseQlirooneFeeInclTax(\Magento\Quote\Model\Quote $quote)
    {
        return $this->getQlirooneFeeInclTax($quote);
    }

    /**
     * @todo Improvement. Proper currency conversion to handle display currencies
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return float|int
     */
    public function getBaseQlirooneFeeExclTax(\Magento\Quote\Model\Quote $quote)
    {
        return $this->getQlirooneFeeExclTax($quote);
    }

    /**
     * Get the summary for cart and checkout
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param float $amount
     * @return array|null
     */
    public function getFeeArray($quote, $amount)
    {
        $feeSetup = $this->getFeeSetup($quote->getStoreId());
        if (!$amount) {
// Ideally, returning null should block the fee from appearing, but it doesn't
//            return null;
        }
        $result = [
            'code' => Config::TOTALS_FEE_CODE,
            'title' => __($feeSetup[Config::CONFIG_FEE_TITLE]),
            'value' => $amount,
        ];
        return $result;
    }

    /**
     * Get the object, used for Totals in both FE and BE on orders, creditnotes and invoices
     *
     * @param int $storeId
     * @param float $amount
     * @return \Magento\Framework\DataObject|null
     */
    public function getFeeObject($storeId, $amount)
    {
        $feeSetup = $this->getFeeSetup($storeId);
        if (!$amount) {
            return null;
        }
        if ($feeSetup) {
            $title = __($feeSetup[Config::CONFIG_FEE_TITLE]);
        } else {
            $title = __('Payment fee');
        }
        $result = new \Magento\Framework\DataObject([
                'code' => Config::TOTALS_FEE_CODE,
                'strong' => false,
                'value' => $amount,
                'label' => $title,
        ]);
        return $result;
    }

    /**
     * Will return fee setup, including an amount of zero
     *
     * @param int $storeId
     * @return array
     */
    public function getFeeSetup($storeId)
    {
        if (!$this->config->isActive($storeId)) {
            return null;
        }
        if (!$this->methodsWithFee) {
            $title = $this->config->getFeeMerchantReference();
            $this->methodsWithFee = array(
                Config::CONFIG_FEE_AMOUNT => 0,
                Config::CONFIG_FEE_TITLE => $title,
            );
        }
        return $this->methodsWithFee;
    }

    /**
     * Picks up the amounts from Fees and runs them through the getTaxPrice function,
     * which changes things depending on display settings etc
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param array $feeCalc
     * @return array
     */
    public function applyDisplayFlagsToFeeArray($quote, $feeCalc)
    {
        if ($feeCalc[Config::CONFIG_FEE_AMOUNT]) {
            $price = $feeCalc[Config::CONFIG_FEE_AMOUNT];
            $feeCalc[Config::CONFIG_FEE_AMOUNT] = $this->getTaxPrice($quote, $price);
        }

        return $feeCalc;
    }

    /**
     * Get current quote from checkout session
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }

    /**
     * Returns the price including or excluding tax, depending on flags being sent in and display settings
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param float $price
     * @param bool|null $includingTax
     * @param bool|null $feeIncludesTax
     * @return float
     */
    private function getTaxPrice($quote, $price, $includingTax = null, $feeIncludesTax = null)
    {
        $pseudoProduct = new \Magento\Framework\DataObject();
        $pseudoProduct->setTaxClassId(
            $this->config->getFeeTaxClass($quote->getStoreId())
        );

        $shippingAddress = null;
        $billingAddress = null;
        $ctc = null;

        if ($feeIncludesTax === null) {
            $feeIncludesTax = $this->config->paymentFeeIncludesTax($quote->getStoreId());
        }

        $price = $this->catalogHelper->getTaxPrice(
            $pseudoProduct,
            $price,
            $includingTax,
            $shippingAddress,
            $billingAddress,
            $ctc,
            $quote->getStoreId(),
            $feeIncludesTax
        );

        return $price;
    }

    /**
     * Returns the price including or excluding tax, NOT depending on display settings
     * Basically a copy of above used function $this->catalogHelper->getTaxPrice
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param float $price
     * @param bool $includingTax
     * @param bool|null $feeIncludesTax
     * @return float
     */
    private function getCalcTaxPrice($quote, $price, $includingTax, $feeIncludesTax = null)
    {
        if (!$price) {
            return $price;
        }

        $product = new \Magento\Framework\DataObject();
        $product->setTaxClassId(
            $this->config->getFeeTaxClass($quote->getStoreId())
        );

        $shippingAddress = null;
        $billingAddress = null;
        $ctc = null;
        $roundPrice = true;

        $store = $this->storeManager->getStore($quote->getStoreId());
        if ($feeIncludesTax === null) {
            $feeIncludesTax = $this->config->paymentFeeIncludesTax($quote->getStoreId());
        }

        $shippingAddressDataObject = null;
        if ($shippingAddress === null) {
            $shippingAddressDataObject =
                $this->convertDefaultTaxAddress($this->customerSession->getDefaultTaxShippingAddress());
        } elseif ($shippingAddress instanceof \Magento\Customer\Model\Address\AbstractAddress) {
            $shippingAddressDataObject = $shippingAddress->getDataModel();
        }

        $billingAddressDataObject = null;
        if ($billingAddress === null) {
            $billingAddressDataObject =
                $this->convertDefaultTaxAddress($this->customerSession->getDefaultTaxBillingAddress());
        } elseif ($billingAddress instanceof \Magento\Customer\Model\Address\AbstractAddress) {
            $billingAddressDataObject = $billingAddress->getDataModel();
        }

        $taxClassKey = $this->taxClassKeyFactory->create();
        $taxClassKey->setType(TaxClassKeyInterface::TYPE_ID)
            ->setValue($product->getTaxClassId());

        if ($ctc === null && $this->customerSession->getCustomerGroupId() != null) {
            $ctc = $this->customerGroupRepository->getById($this->customerSession->getCustomerGroupId())
                ->getTaxClassId();
        }

        $customerTaxClassKey = $this->taxClassKeyFactory->create();
        $customerTaxClassKey->setType(TaxClassKeyInterface::TYPE_ID)
            ->setValue($ctc);

        $item = $this->quoteDetailsItemFactory->create();
        $item->setQuantity(1)
            ->setCode($product->getSku())
            ->setShortDescription($product->getShortDescription())
            ->setTaxClassKey($taxClassKey)
            ->setIsTaxIncluded($feeIncludesTax)
            ->setType('product')
            ->setUnitPrice($price);

        $quoteDetails = $this->quoteDetailsFactory->create();
        $quoteDetails->setShippingAddress($shippingAddressDataObject)
            ->setBillingAddress($billingAddressDataObject)
            ->setCustomerTaxClassKey($customerTaxClassKey)
            ->setItems([$item])
            ->setCustomerId($this->customerSession->getCustomerId());

        $storeId = null;
        if ($store) {
            $storeId = $store->getId();
        }
        $taxDetails = $this->taxCalculation->calculateTax($quoteDetails, $storeId, $roundPrice);
        $items = $taxDetails->getItems();
        $taxDetailsItem = array_shift($items);

        if ($includingTax) {
            $price = $taxDetailsItem->getPriceInclTax();
        } else {
            $price = $taxDetailsItem->getPrice();
        }

        if ($roundPrice) {
            return $this->priceCurrency->round($price);
        } else {
            return $price;
        }
    }

    /**
     * @param array $taxAddress
     * @return \Magento\Customer\Api\Data\AddressInterface|null
     */
    private function convertDefaultTaxAddress(array $taxAddress = null)
    {
        if (empty($taxAddress)) {
            return null;
        }
        /** @var \Magento\Customer\Api\Data\AddressInterface $addressDataObject */
        $addressDataObject = $this->addressFactory->create()
            ->setCountryId($taxAddress['country_id'])
            ->setPostcode($taxAddress['postcode']);

        if (isset($taxAddress['region_id'])) {
            $addressDataObject->setRegion($this->regionFactory->create()->setRegionId($taxAddress['region_id']));
        }
        return $addressDataObject;
    }

}