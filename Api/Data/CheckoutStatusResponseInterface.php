<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * Update Shipping Methods notification callback response interface
 */
interface CheckoutStatusResponseInterface extends ContainerInterface
{
    const CALLBACK_RESPONSE = 'CallbackResponse';

    const RESPONSE_NOTIFICATIONS_DISABLED = 'Notifications disabled';
    const RESPONSE_AUTHENTICATE_ERROR = 'Authenticate error';
    const RESPONSE_RECEIVED = 'received';
    const RESPONSE_ORDER_NOT_FOUND = 'Order not found';

    /**
     * @return string
     */
    public function getCallbackResponse();

    /**
     * @param string $value
     * @return $this
     */
    public function setCallbackResponse($value);
}
