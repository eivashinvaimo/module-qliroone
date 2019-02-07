<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use Qliro\QliroOne\Model\Security\AjaxToken;

/**
 * QliroOne Cehckout config provider class
 */
class CheckoutConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Qliro\QliroOne\Model\Security\AjaxToken
     */
    private $ajaxToken;

    /**
     * @var \Qliro\QliroOne\Model\Config
     */
    private $qliroConfig;

    /**
     * @var Fee
     */
    private $fee;

    /**
     * Inject dependencies
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Qliro\QliroOne\Model\Security\AjaxToken $ajaxToken
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Qliro\QliroOne\Model\Config $qliroConfig
     * @param \Qliro\QliroOne\Model\Fee $fee
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        AjaxToken $ajaxToken,
        Session $checkoutSession,
        Config $qliroConfig,
        \Qliro\QliroOne\Model\Fee $fee
    ) {
        $this->quote = $checkoutSession->getQuote();
        $this->storeManager = $storeManager;
        $this->ajaxToken = $ajaxToken;
        $this->qliroConfig = $qliroConfig;
        $this->fee = $fee;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $feeSetup = $this->fee->getFeeSetup($this->quote->getStoreId());
        $feeSetup = $this->fee->applyDisplayFlagsToFeeArray($this->quote, $feeSetup);
        return [
            'qliro' => [
                'enabled' => $this->qliroConfig->isActive(),
                'isDebug' => $this->qliroConfig->isDebugMode(),
                'isEagerCheckoutRefresh' => $this->qliroConfig->isEagerCheckoutRefresh(),
                'checkoutTitle' => $this->qliroConfig->getTitle(),
                'securityToken' => $this->ajaxToken->setQuote($this->quote)->getToken(),
                'updateQuoteUrl' => $this->getUrl('checkout/qliro_ajax/updateQuote'),
                'updateCustomerUrl' => $this->getUrl('checkout/qliro_ajax/updateCustomer'),
                'updateShippingMethodUrl' => $this->getUrl('checkout/qliro_ajax/updateShippingMethod'),
                'updateShippingPriceUrl' => $this->getUrl('checkout/qliro_ajax/updateShippingPrice'),
                'updatePaymentMethodUrl' => $this->getUrl('checkout/qliro_ajax/updatePaymentMethod'),
                'pollSuccessUrl' => $this->getUrl('checkout/qliro_ajax/pollSuccess'),
                'qliroone_fee' => [
                    'fee_setup' => $feeSetup,
                ],
            ],
        ];
    }

    /**
     * Get a store-specific URL with provided path
     *
     * @param string $path
     * @return string
     */
    private function getUrl($path)
    {
        $store = $this->storeManager->getStore();

        return $store->getUrl($path);
    }
}
