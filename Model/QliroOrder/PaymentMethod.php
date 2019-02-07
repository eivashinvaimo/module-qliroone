<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder;

use Qliro\QliroOne\Api\Data\QliroOrderPaymentMethodInterface;

/**
 * QliroOne order payment method class
 */
class PaymentMethod implements QliroOrderPaymentMethodInterface
{
    /**
     * @var string
     */
    private $paymentMethodName;

    /**
     * @var string
     */
    private $paymentTypeCode;

    /**
     * Getter.
     *
     * @return string
     */
    public function getPaymentMethodName()
    {
        return $this->paymentMethodName;
    }

    /**
     * @param string $paymentMethodName
     * @return PaymentMethod
     */
    public function setPaymentMethodName($paymentMethodName)
    {
        $this->paymentMethodName = $paymentMethodName;

        return $this;
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function getPaymentTypeCode()
    {
        return $this->paymentTypeCode;
    }

    /**
     * @param string $paymentTypeCode
     * @return PaymentMethod
     */
    public function setPaymentTypeCode($paymentTypeCode)
    {
        $this->paymentTypeCode = $paymentTypeCode;

        return $this;
    }
}
