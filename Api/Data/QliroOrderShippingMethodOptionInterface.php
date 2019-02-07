<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * QliroOne Order Shipping Method Option interface
 *
 * @api
 */
interface QliroOrderShippingMethodOptionInterface extends ContainerInterface
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
     * @return array
     */
    public function getDescriptions();

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
     * @param array $value
     * @return $this
     */
    public function setDescriptions($value);
}
