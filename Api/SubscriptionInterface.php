<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api;

/**
 * Subscription interface
 *
 * @api
 */
interface SubscriptionInterface
{
    /**
     * Add/activate subscription for email
     *
     * @param string $email
     * @param int $storeId
     * @return void
     */
    public function addSubscription($email, $storeId);
}
