<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * Qliro Order Create Request interface
 *
 * @api
 */
interface QliroOrderCreateRequestInterface extends ContainerInterface
{
    /**
     * @return string
     */
    public function getMerchantReference();

    /**
     * @return string
     */
    public function getMerchantApiKey();

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @return string
     */
    public function getLanguage();

    /**
     * @return string
     */
    public function getMerchantConfirmationUrl();

    /**
     * @return string
     */
    public function getMerchantTermsUrl();

    /**
     * @return \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[]
     */
    public function getOrderItems();

    /**
     * @return string
     */
    public function getMerchantCheckoutStatusPushUrl();

    /**
     * @return string
     */
    public function getMerchantOrderManagementStatusPushUrl();

    /**
     * @return string
     */
    public function getMerchantOrderValidationUrl();

    /**
     * @return string
     */
    public function getMerchantOrderAvailableShippingMethodsUrl();

    /**
     * @return string
     */
    public function getMerchantIntegrityPolicyUrl();

    /**
     * @return string
     */
    public function getBackgroundColor();

    /**
     * @return string
     */
    public function getPrimaryColor();

    /**
     * @return string
     */
    public function getCallToActionColor();

    /**
     * @return string
     */
    public function getCallToActionHoverColor();

    /**
     * @return int
     */
    public function getCornerRadius();

    /**
     * @return int
     */
    public function getButtonCornerRadius();

    /**
     * @return \Qliro\QliroOne\Api\Data\QliroOrderCustomerInterface
     */
    public function getCustomerInformation();

    /**
     * @return string
     */
    public function getAvailableShippingMethods();

    /**
     * @return string
     */
    public function getEnforcedJuridicalType();

    /**
     * @return int
     */
    public function getMinimumCustomerAge();

    /**
     * @return bool
     */
    public function getAskForNewsletterSignup();

    /**
     * @return bool
     */
    public function getRequireIdentityVerification();

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantReference($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantApiKey($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCountry($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCurrency($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setLanguage($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantConfirmationUrl($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantTermsUrl($value);

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterface[] $value
     * @return $this
     */
    public function setOrderItems($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantCheckoutStatusPushUrl($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantOrderManagementStatusPushUrl($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantOrderValidationUrl($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantOrderAvailableShippingMethodsUrl($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantIntegrityPolicyUrl($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setBackgroundColor($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setPrimaryColor($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCallToActionColor($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCallToActionHoverColor($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setCornerRadius($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setButtonCornerRadius($value);

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderCustomerInterface $value
     * @return $this
     */
    public function setCustomerInformation($value);

    /**
     * @param array $value
     * @return $this
     */
    public function setAvailableShippingMethods($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setEnforcedJuridicalType($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setMinimumCustomerAge($value);

    /**
     * @param bool $value
     * @return $this
     */
    public function setAskForNewsletterSignup($value);

    /**
     * @param bool $value
     * @return $this
     */
    public function setRequireIdentityVerification($value);
}
