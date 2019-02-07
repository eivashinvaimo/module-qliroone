<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Builder;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\StoreManagerInterface;
use Qliro\QliroOne\Api\Data\QliroOrderCreateRequestInterfaceFactory;
use Qliro\QliroOne\Api\Data\QliroOrderShippingMethodInterfaceFactory;
use Qliro\QliroOne\Api\GeoIpResolverInterface;
use Qliro\QliroOne\Api\LanguageMapperInterface;
use Qliro\QliroOne\Model\Config;
use Qliro\QliroOne\Model\Security\CallbackToken;
use \Magento\Framework\Url\QueryParamsResolverInterface;
use Magento\Store\Model\Information;

/**
 * QliroOne Order create request builder class
 */
class CreateRequestBuilder
{
    /**
     * @var string
     */
    private $generatedToken;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderCreateRequestInterfaceFactory
     */
    private $createRequestFactory;

    /**
     * @var \Qliro\QliroOne\Api\LanguageMapperInterface
     */
    private $languageMapper;

    /**
     * @var \Qliro\QliroOne\Model\Config
     */
    private $qliroConfig;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderShippingMethodInterfaceFactory
     */
    private $shippingMethodFactory;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Builder\CustomerBuilder
     */
    private $customerBuilder;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Builder\OrderItemsBuilder
     */
    private $orderItemsBuilder;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Qliro\QliroOne\Api\GeoIpResolverInterface
     */
    private $geoIpResolver;

    /**
     * @var \Qliro\QliroOne\Model\Security\CallbackToken
     */
    private $callbackToken;

    /**
     * @var \Magento\Framework\Url\QueryParamsResolverInterface
     */
    private $queryParamsResolver;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Builder\ShippingMethodsBuilder
     */
    private $shippingMethodsBuilder;

    /**
     * @var \Magento\Store\Model\Information
     */
    private $information;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderCreateRequestInterfaceFactory $createRequestFactory
     * @param \Qliro\QliroOne\Model\QliroOrder\Builder\CustomerBuilder $customerBuilderm
     * @param \Qliro\QliroOne\Model\QliroOrder\Builder\OrderItemsBuilder $orderItemsBuilder
     * @param \Qliro\QliroOne\Api\Data\QliroOrderShippingMethodInterfaceFactory $shippingMethodFactory
     * @param \Qliro\QliroOne\Api\LanguageMapperInterface $languageMapper
     * @param \Qliro\QliroOne\Model\Config $qliroConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Qliro\QliroOne\Api\GeoIpResolverInterface $geoIpResolver
     * @param \Qliro\QliroOne\Model\Security\CallbackToken $callbackToken
     * @param \Magento\Framework\Url\QueryParamsResolverInterface $queryParamsResolver
     * @param \Qliro\QliroOne\Model\QliroOrder\Builder\ShippingMethodsBuilder $shippingMethodsBuilder
     * @param \Magento\Store\Model\Information $information
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        QliroOrderCreateRequestInterfaceFactory $createRequestFactory,
        CustomerBuilder $customerBuilderm,
        OrderItemsBuilder $orderItemsBuilder,
        QliroOrderShippingMethodInterfaceFactory $shippingMethodFactory,
        LanguageMapperInterface $languageMapper,
        Config $qliroConfig,
        ScopeConfigInterface $scopeConfig,
        Session $session,
        StoreManagerInterface $storeManager,
        GeoIpResolverInterface $geoIpResolver,
        CallbackToken $callbackToken,
        QueryParamsResolverInterface $queryParamsResolver,
        ShippingMethodsBuilder $shippingMethodsBuilder,
        Information $information,
        ManagerInterface $eventManager
    ) {
        $this->createRequestFactory = $createRequestFactory;
        $this->languageMapper = $languageMapper;
        $this->qliroConfig = $qliroConfig;
        $this->scopeConfig = $scopeConfig;
        $this->shippingMethodFactory = $shippingMethodFactory;
        $this->customerBuilder = $customerBuilderm;
        $this->orderItemsBuilder = $orderItemsBuilder;
        $this->session = $session;
        $this->storeManager = $storeManager;
        $this->geoIpResolver = $geoIpResolver;
        $this->callbackToken = $callbackToken;
        $this->queryParamsResolver = $queryParamsResolver;
        $this->shippingMethodsBuilder = $shippingMethodsBuilder;
        $this->information = $information;
        $this->eventManager = $eventManager;
    }

    /**
     * Set quote for data extraction
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return $this
     */
    public function setQuote(CartInterface $quote)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Generate a QliroOne order create request object
     *
     * @return \Qliro\QliroOne\Api\Data\QliroOrderCreateRequestInterface
     * @throws \Exception
     * @todo: should we always supply shipping methods, or should it be a configuration?
     * @todo: what about virtual quotes, they should not have any shipping methods or what?
     */
    public function create()
    {
        if (empty($this->quote)) {
            throw new \LogicException('Quote entity is not set.');
        }

        $createRequest = $this->prepareCreateRequest();

        $orderItems = $this->orderItemsBuilder->setQuote($this->quote)->create();

        $createRequest->setOrderItems($orderItems);
        $presetAddress = $this->qliroConfig->presetAddress();
        $shippingAddress = $this->quote->getShippingAddress();
        if ($presetAddress && empty($shippingAddress->getPostcode())) {
            /* set a fake address since we don't have the real one yet */
            $storeInfo = $this->information->getStoreInformationObject($this->quote->getStore());
            if (!empty($storeInfo)) {
                $shippingAddress->addData([
                    'company' => $storeInfo->getData('name'),
                    'telephone' => $storeInfo->getData('phone'),
                    'street' => sprintf(
                        "%s\n%s",
                        $storeInfo->getData('street_line1'),
                        $storeInfo->getData('street_line2')
                    ),
                    'city' => $storeInfo->getData('city'),
                    'postcode' => str_replace(' ', '', $storeInfo->getData('postcode')),
                    'region_id' => $storeInfo->getData('region_id'),
                    'country_id' => $storeInfo->getData('country_id'),
                    'region' => $storeInfo->getData('region'),
                ]);
            }
        }
        $shippingAddress->setCollectShippingRates(true)->collectShippingRates()->save();
        $shippingMethods = $this->shippingMethodsBuilder->setQuote($this->quote)->create();
        $availableShippingMethods = $shippingMethods->getAvailableShippingMethods();
        if (!empty($storeInfo)) {
            $shippingAddress->clearInstance()->save();
        }
        $createRequest->setAvailableShippingMethods($availableShippingMethods);

        if ($this->session->isLoggedIn()) {
            $customerInfo = $this->customerBuilder->setCustomer($this->quote->getCustomer())->create();
            $createRequest->setCustomerInformation($customerInfo);
        }

        $this->quote->getBillingAddress()->setCountryId($createRequest->getCountry());
        $this->quote->getShippingAddress()->setCountryId($createRequest->getCountry());
        $this->quote->save();

        $this->eventManager->dispatch(
            'qliroone_order_create_request_build_after',
            [
                'quote' => $this->quote,
                'container' => $createRequest,
            ]
        );

        $this->quote = null;

        return $createRequest;
    }

    /**
     * @return \Qliro\QliroOne\Api\Data\QliroOrderCreateRequestInterface
     */
    private function prepareCreateRequest()
    {
        /** @var \Magento\Quote\Api\Data\CurrencyInterface $currencies */
        $currencies = $this->quote->getCurrency();

        /** @var \Qliro\QliroOne\Api\Data\QliroOrderCreateRequestInterface $createRequest */
        $createRequest = $this->createRequestFactory->create();

        $createRequest->setCurrency($currencies->getQuoteCurrencyCode());
        $createRequest->setLanguage($this->languageMapper->getLanguage());
        $createRequest->setCountry($this->getCountry());

        $termsUrl = $this->qliroConfig->getTermsUrl();
        $createRequest->setMerchantTermsUrl($termsUrl ? $termsUrl : $this->getUrl('/'));
        $createRequest->setMerchantIntegrityPolicyUrl($this->qliroConfig->getIntegrityPolicyUrl());

        $createRequest->setMerchantConfirmationUrl($this->getUrl('checkout/qliro/success'));

        $createRequest->setMerchantCheckoutStatusPushUrl(
            $this->getCallbackUrl('checkout/qliro_callback/checkoutStatus')
        );

        $createRequest->setMerchantOrderManagementStatusPushUrl(
            $this->getCallbackUrl('checkout/qliro_callback/transactionStatus')
        );

        $createRequest->setMerchantOrderValidationUrl($this->getCallbackUrl('checkout/qliro_callback/validate'));

        $createRequest->setMerchantOrderAvailableShippingMethodsUrl(
            $this->getCallbackUrl('checkout/qliro_callback/shippingMethods')
        );

        $createRequest->setBackgroundColor($this->qliroConfig->getStylingBackgroundColor());
        $createRequest->setPrimaryColor($this->qliroConfig->getStylingPrimaryColor());
        $createRequest->setCallToActionColor($this->qliroConfig->getStylingCallToActionColor());
        $createRequest->setCallToActionHoverColor($this->qliroConfig->getStylingHoverColor());
        $createRequest->setCornerRadius($this->qliroConfig->getStylingRadius());
        $createRequest->setButtonCornerRadius($this->qliroConfig->getStylingButtonCurnerRadius());

        $createRequest->setEnforcedJuridicalType(null);
        $createRequest->setMinimumCustomerAge(null);
        $createRequest->setAskForNewsletterSignup($this->qliroConfig->shouldAskForNewsletterSignup());
        $createRequest->setRequireIdentityVerification(false);

        return $createRequest;
    }

    /**
     * Get a country code, either from default config setting, or from a GeoIP resolver
     *
     * @return string
     */
    private function getCountry()
    {
        $countryCode = null;

        if ($this->qliroConfig->isUseGeoIp()) {
            $countryCode = $this->geoIpResolver->getCountryCode($this->quote->getRemoteIp());
        }

        if (empty($countryCode)) {
            $countryCode = $this->scopeConfig->getValue(\Magento\Directory\Helper\Data::XML_PATH_DEFAULT_COUNTRY);
        }

        return $countryCode;
    }

    /**
     * Get a callback URL with provided path and generated token
     *
     * @param string $path
     * @return string
     */
    private function getCallbackUrl($path)
    {
        $params['_query']['token'] = $this->generateCallbackToken();

        if ($this->qliroConfig->isDebugMode()) {
            $params['_query']['XDEBUG_SESSION_START'] = $this->qliroConfig->getCallbackXdebugSessionFlagName();
        }

        if ($this->qliroConfig->redirectCallbacks() && ($baseUri = $this->qliroConfig->getCallbackUri())) {
            $url = implode('/', [rtrim($baseUri, '/'), ltrim($path, '/')]);

            $this->queryParamsResolver->addQueryParams($params['_query']);
            $queryString = $this->queryParamsResolver->getQuery();
            $url .= '?' . $queryString;

            return $this->applyHttpAuth($url);
        }

        return $this->applyHttpAuth($this->getUrl($path, $params));
    }

    /**
     * Apply HTTP authentication credentials if specified
     *
     * @param string $url
     * @return string
     */
    private function applyHttpAuth($url)
    {
        if ($this->qliroConfig->isHttpAuthEnabled() && preg_match('#^(https?://)(.+)$#', $url, $match)) {
            $authUsername = $this->qliroConfig->getCallbackHttpAuthUsername();
            $authPassword = $this->qliroConfig->getCallbackHttpAuthPassword();

            $url = sprintf('%s%s:%s@%s', $match[1], \urlencode($authUsername), \urlencode($authPassword), $match[2]);
        }

        return $url;
    }

    /**
     * Get a store-specific URL with provided path and optional parameters
     *
     * @param string $path
     * @param array $params
     * @return string
     */
    private function getUrl($path, $params = [])
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore();

        return $store->getUrl($path, $params);
    }

    /**
     * @return string
     */
    private function generateCallbackToken()
    {
        if (!$this->generatedToken) {
            $this->generatedToken = $this->callbackToken->getToken();
        }

        return $this->generatedToken;
    }
}
