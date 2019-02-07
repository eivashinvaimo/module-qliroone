<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Qliro\QliroOne\Model;

use Magento\Payment\Model\Method\Adapter;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const QLIROONE_ACTIVE = 'active';
    const QLIROONE_TITLE = 'title';
    const QLIROONE_DEBUG = 'debug';
    const QLIROONE_EAGER_CHECKOUT_REFRESH = 'eager_checkout_refresh';

    const QLIROONE_GEOIP = 'api/geoip';
    const QLIROONE_LOGGING_LEVEL = 'api/logging';
    const QLIROONE_ORDER_STATUS = 'api/order_status';
    const QLIROONE_ALLOW_SPECIFIC = 'api/allowspecific';
    const QLIROONE_COUNTRIES = 'api/shipping_countries';
    const QLIROONE_CAPTURE_ON_SHIPMENT = 'api/capture_on_shipment';
    const QLIROONE_CAPTURE_ON_INVOICE = 'api/capture_on_invoice';
    const QLIROONE_NEWSLETTER_SIGNUP = 'api/newsletter_signup';
    const QLIROONE_REQUIRE_IDENTITY_VERIFICATION = 'api/require_identity_verification';

    const QLIROONE_API_TYPE = 'qliro_api/type';
    const QLIROONE_MERCHANT_API_KEY = 'qliro_api/merchant_api_key';
    const QLIROONE_MERCHANT_API_SECRET = 'qliro_api/merchant_api_secret';
    const QLIROONE_PRESET_ADDRESS = 'qliro_api/preset_address';

    const QLIROONE_STYLING_BACKGROUND = 'styling/background_color';
    const QLIROONE_STYLING_PRIMARY = 'styling/primary_color';
    const QLIROONE_STYLING_CALL_TO_ACTION = 'styling/call_to_action_color';
    const QLIROONE_STYLING_HOVER = 'styling/call_to_action_hover_color';
    const QLIROONE_STYLING_RADIUS = 'styling/corner_radius';
    const QLIROONE_STYLING_BUTTON_CORNER = 'styling/button_corner_radius';

    const QLIROONE_FEE_MERCHANT_REFERENCE = 'merchant/fee_merchant_reference';
    const QLIROONE_TERMS_URL = 'merchant/terms_url';
    const QLIROONE_INTEGRITY_POLICY_URL = 'merchant/integrity_policy_url';

    const QLIROONE_XDEBUG_SESSION_FLAG_NAME = 'callback/xdebug_session_flag_name';
    const QLIROONE_REDIRECT_CALLBACKS = 'callback/redirect_callbacks';
    const QLIROONE_CALLBACK_URI = 'callback/callback_uri';
    const QLIROONE_ENABLE_HTTP_AUTH = 'callback/enable_http_auth';
    const QLIROONE_HTTP_AUTH_USERNAME = 'callback/http_auth_username';
    const QLIROONE_HTTP_AUTH_PASSWORD = 'callback/http_auth_password';

    const QLIROONE_ADDITIONAL_INFO_REFERENCE = 'qliro_reference';
    const QLIROONE_ADDITIONAL_INFO_QLIRO_ORDER_ID = 'qliro_order_id';
    const QLIROONE_ADDITIONAL_INFO_PAYMENT_METHOD_CODE = 'qliro_payment_method_code';
    const QLIROONE_ADDITIONAL_INFO_PAYMENT_METHOD_NAME = 'qliro_payment_method_name';

    const CONFIG_FEE_AMOUNT = 'fee';
    const CONFIG_FEE_TITLE = 'description';

    const TOTALS_FEE_CODE = 'qliroone_fee';
    const TOTALS_FEE_CODE_TAX = 'qliroone_fee_tax';
    const TOTALS_BASE_FEE_CODE = 'base_qliroone_fee';
    const TOTALS_BASE_FEE_CODE_TAX = 'base_qliroone_fee_tax';

    /**
     * Payment Fee tax class
     */
    const XML_PATH_TAX_CLASS = 'tax/classes/qliroone_fee_tax_class';

    /**
     * @todo Improvement for proper module. Make use of this setting, it is not at the moment
     *
     * Shopping cart display settings
     */
    const XML_PATH_PRICE_DISPLAY_CART_PAYMENT_FEE = 'tax/cart_display/qliroone_fee';

    /**
     * @todo Improvement for proper module. Make use of this setting, it is not at the moment
     *
     * Sales display settings
     */
    const XML_PATH_PRICE_DISPLAY_SALES_PAYMENT_FEE = 'tax/sales_display/qliroone_fee';

    /**
     * tax calculation for payment fee
     */
    const CONFIG_XML_PATH_PAYMENT_FEE_INCLUDES_TAX = 'tax/calculation/qliroone_fee_includes_tax';

    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * Inject dependencies
     *
     * @param \Magento\Payment\Model\Method\Adapter $adapter
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     */
    public function __construct(
        Adapter $adapter,
        ScopeConfigInterface $config
    ) {
        $this->adapter = $adapter;
        $this->config = $config;
    }

    /**
     * Check if the payment method is active
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->adapter->getConfigData(self::QLIROONE_ACTIVE);
    }

    /**
     * Check if the GeoIP capability should be used
     *
     * @return bool
     */
    public function isUseGeoIp()
    {
        return (bool)$this->adapter->getConfigData(self::QLIROONE_GEOIP);
    }

    /**
     * Check whether debug mode is on
     *
     * @return bool
     */
    public function isDebugMode()
    {
        return (bool)$this->adapter->getConfigData(self::QLIROONE_DEBUG);
    }

    /**
     * Check whether an Eager Checkout Refresh mode is on
     *
     * @return bool
     */
    public function isEagerCheckoutRefresh()
    {
        return (bool)$this->adapter->getConfigData(self::QLIROONE_EAGER_CHECKOUT_REFRESH);
    }

    /**
     * Check whether callbacks should be routed through a public server
     *
     * @return bool
     */
    public function redirectCallbacks()
    {
        return (bool)$this->adapter->getConfigData(self::QLIROONE_REDIRECT_CALLBACKS);
    }

    /**
     * Get url for callback server
     *
     * @return string
     */
    public function getCallbackUri()
    {
        return (string)$this->adapter->getConfigData(self::QLIROONE_CALLBACK_URI);
    }

    /**
     * @return int
     */
    public function getLoggingLevel()
    {
        return (int)$this->adapter->getConfigData(self::QLIROONE_LOGGING_LEVEL);
    }

    /**
     * Get payment method title
     *
     * @return string
     */
    public function getTitle()
    {
        return (string)$this->adapter->getConfigData(self::QLIROONE_TITLE);
    }

    /**
     * Get the status order will end up on successful payment
     *
     * @return string
     */
    public function getOrderStatus()
    {
        return (string)$this->adapter->getConfigData(self::QLIROONE_ORDER_STATUS);
    }

    /**
     * @return bool
     */
    public function getAllowSpecific()
    {
        return (bool)$this->adapter->getConfigData(self::QLIROONE_ALLOW_SPECIFIC);
    }

    /**
     * @return string
     */
    public function getSpecificCountries()
    {
        return (string)$this->adapter->getConfigData(self::QLIROONE_COUNTRIES);
    }

    /**
     * @return bool
     */
    public function shouldCaptureOnShipment()
    {
        return (int)$this->adapter->getConfigData(self::QLIROONE_CAPTURE_ON_SHIPMENT);
    }

    /**
     * @return bool
     */
    public function shouldCaptureOnInvoice()
    {
        return (int)$this->adapter->getConfigData(self::QLIROONE_CAPTURE_ON_INVOICE);
    }

    /**
     * @return bool
     */
    public function shouldAskForNewsletterSignup()
    {
        return (int)$this->adapter->getConfigData(self::QLIROONE_NEWSLETTER_SIGNUP);
    }

    /**
     * @return bool
     */
    public function requireIdentityVerification()
    {
        return (int)$this->adapter->getConfigData(self::QLIROONE_REQUIRE_IDENTITY_VERIFICATION);
    }

    /**
     * Get API type (may be either "sandbox" or "prod"
     *
     * @return string
     */
    public function getApiType()
    {
        return (string)$this->adapter->getConfigData(self::QLIROONE_API_TYPE);
    }

    /**
     * @return string
     */
    public function getMerchantApiKey()
    {
        return (string)$this->adapter->getConfigData(self::QLIROONE_MERCHANT_API_KEY);
    }

    /**
     * @return string
     */
    public function getMerchantApiSecret()
    {
        return (string)$this->adapter->getConfigData(self::QLIROONE_MERCHANT_API_SECRET);
    }

    /**
     * @return bool
     */
    public function presetAddress()
    {
        return (bool)$this->adapter->getConfigData(self::QLIROONE_PRESET_ADDRESS);
    }

    /**
     * @return string
     */
    public function getStylingBackgroundColor()
    {
        return $this->checkHexColor($this->adapter->getConfigData(self::QLIROONE_STYLING_BACKGROUND));
    }

    /**
     * @return string
     */
    public function getStylingPrimaryColor()
    {
        return $this->checkHexColor($this->adapter->getConfigData(self::QLIROONE_STYLING_PRIMARY));
    }

    /**
     * @return string
     */
    public function getStylingCallToActionColor()
    {
        return $this->checkHexColor($this->adapter->getConfigData(self::QLIROONE_STYLING_CALL_TO_ACTION));
    }

    /**
     * @return string
     */
    public function getStylingHoverColor()
    {
        return $this->checkHexColor($this->adapter->getConfigData(self::QLIROONE_STYLING_HOVER));
    }

    /**
     * @return int
     */
    public function getStylingRadius()
    {
        return (int)$this->adapter->getConfigData(self::QLIROONE_STYLING_RADIUS);
    }

    /**
     * @return int
     */
    public function getStylingButtonCurnerRadius()
    {
        return (int)$this->adapter->getConfigData(self::QLIROONE_STYLING_BUTTON_CORNER);
    }

    /**
     * @return string
     */
    public function getFeeMerchantReference()
    {
        return (string)$this->adapter->getConfigData(self::QLIROONE_FEE_MERCHANT_REFERENCE);
    }

    /**
     * @return string
     */
    public function getTermsUrl()
    {
        $value = $this->adapter->getConfigData(self::QLIROONE_TERMS_URL);

        return $value ? (string)$value : null;
    }

    /**
     * @return string
     */
    public function getIntegrityPolicyUrl()
    {
        $value = $this->adapter->getConfigData(self::QLIROONE_INTEGRITY_POLICY_URL);

        return $value ? (string)$value : null;
    }

    /**
     * Check if HTTP Auth for callbacks is enabled
     *
     * @return bool
     */
    public function isHttpAuthEnabled()
    {
        return (bool)$this->adapter->getConfigData(self::QLIROONE_ENABLE_HTTP_AUTH);
    }

    /**
     * Get an HTTP Auth username for callbacks
     *
     * @return string
     */
    public function getCallbackHttpAuthUsername()
    {
        return (string)$this->adapter->getConfigData(self::QLIROONE_HTTP_AUTH_USERNAME);
    }

    /**
     * Get an HTTP Auth password for callbacks
     *
     * @return string
     */
    public function getCallbackHttpAuthPassword()
    {
        return (string)$this->adapter->getConfigData(self::QLIROONE_HTTP_AUTH_PASSWORD);
    }

    /**
     * Get XDebug session flag name for callbacks
     *
     * @return string
     */
    public function getCallbackXdebugSessionFlagName()
    {
        if (!$this->isDebugMode()) {
            return '';
        }
        return (string)$this->adapter->getConfigData(self::QLIROONE_XDEBUG_SESSION_FLAG_NAME);
    }

    /**
     * Dummy config for payment method compatibility
     *
     * @return boolean
     */
    public function shouldUpdateQuoteBilling()
    {
        return true;
    }

    /**
     * Dummy config for payment method compatibility
     *
     * @return boolean
     */
    public function shouldUpdateQuoteShipping()
    {
        return true;
    }

    /**
     * Check if the value a proper HEX color code, return null otherwise
     *
     * @param string $value
     * @return string|null
     */
    private function checkHexColor($value)
    {
        return preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', trim($value)) ? trim($value) : null;
    }

    /**
     * Get TaxClass for Fee
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return string|null
     */
    public function getFeeTaxClass($store = null)
    {
        return $this->config->getValue(
            self::XML_PATH_TAX_CLASS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check ability to display prices including tax for payment fee in shopping cart
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function displayCartPaymentFeeIncludeTaxPrice($store = null)
    {
        $configValue = $this->config->getValue(
            self::XML_PATH_PRICE_DISPLAY_CART_PAYMENT_FEE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue == \Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH ||
            $configValue == \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Check ability to display prices excluding tax for payment fee in shopping cart
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function displayCartPaymentFeeExcludeTaxPrice($store = null)
    {
        $configValue = $this->config->getValue(
            self::XML_PATH_PRICE_DISPLAY_CART_PAYMENT_FEE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue == \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    /**
     * Check ability to display both prices for payment fee in shopping cart
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function displayCartPaymentFeeBothPrices($store = null)
    {
        $configValue = $this->config->getValue(
            self::XML_PATH_PRICE_DISPLAY_CART_PAYMENT_FEE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue == \Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH;
    }

    /**
     * Check ability to display prices including tax for payment fee in backend sales
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function displaySalesPaymentFeeIncludeTaxPrice($store = null)
    {
        $configValue = $this->config->getValue(
            self::XML_PATH_PRICE_DISPLAY_SALES_PAYMENT_FEE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue == \Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH ||
            $configValue == \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Check ability to display prices excluding tax for payment fee in backend sales
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function displaySalesPaymentFeeExcludeTaxPrice($store = null)
    {
        $configValue = $this->config->getValue(
            self::XML_PATH_PRICE_DISPLAY_SALES_PAYMENT_FEE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue == \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    /**
     * Check ability to display both prices for payment fee in backend sales
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function displaySalesPaymentFeeBothPrices($store = null)
    {
        $configValue = $this->config->getValue(
            self::XML_PATH_PRICE_DISPLAY_SALES_PAYMENT_FEE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue == \Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH;
    }

    /**
     * Check if shipping prices include tax
     *
     * @param   null|string|bool|int|Store $store
     * @return  bool
     */
    public function paymentFeeIncludesTax($store = null)
    {
        $configValue = $this->config->getValue(
            self::CONFIG_XML_PATH_PAYMENT_FEE_INCLUDES_TAX,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return (bool)$configValue;
    }
}
