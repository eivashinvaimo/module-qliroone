<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * QliroOne Order Shipping Method interface
 *
 * @api
 */
interface QliroOrderShippingMethodInterface extends ContainerInterface
{
    /**
     * @return string
     */
    public function getMerchantReference();

    /**
     * @return string
     */
    public function getDisplayName();

    /**
     * @return float
     */
    public function getPriceIncVat();

    /**
     * @return float
     */
    public function getPriceExVat();

    /**
     * @return array
     */
    public function getDescriptions();

    /**
     * @return string
     */
    public function getBrand();

    /**
     * @return bool
     */
    public function getSupportsAccessCode();

    /**
     * @return \Qliro\QliroOne\Api\Data\QliroOrderShippingMethodOptionInterface[]
     */
    public function getSecondaryOptions();

    /**
     * @return string
     */
    public function getShippingFeeMerchantReference();

    /**
     * @return bool
     */
    public function getSupportsDynamicSecondaryOptions();

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantReference($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setDisplayName($value);

    /**
     * @param float $value
     * @return $this
     */
    public function setPriceIncVat($value);

    /**
     * @param float $value
     * @return $this
     */
    public function setPriceExVat($value);

    /**
     * @param array $value
     * @return $this
     */
    public function setDescriptions($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setBrand($value);

    /**
     * @param bool $value
     * @return $this
     */
    public function setSupportsAccessCode($value);

    /**
     * @param \Qliro\QliroOne\Api\Data\QliroOrderShippingMethodOptionInterface[] $value
     * @return $this
     */
    public function setSecondaryOptions($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setShippingFeeMerchantReference($value);

    /**
     * @param bool $value
     * @return $this
     */
    public function setSupportsDynamicSecondaryOptions($value);
}
