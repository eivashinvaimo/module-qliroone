<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Notification;

use Qliro\QliroOne\Api\Data\CheckoutStatusResponseInterface;

/**
 * Checkout Status Push Response class
 */
class CheckoutStatusResponse implements CheckoutStatusResponseInterface
{
    /**
     * @var string
     */
    private $callbackResponse;

    /**
     * @return string
     */
    public function getCallbackResponse()
    {
        return $this->callbackResponse;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setCallbackResponse($value)
    {
        $this->callbackResponse = $value;

        return $this;
    }
}
