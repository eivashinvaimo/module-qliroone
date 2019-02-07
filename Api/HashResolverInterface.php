<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api;

use Magento\Quote\Api\Data\CartInterface;

/**
 * Hash Resolver interface
 */
interface HashResolverInterface
{
    const HASH_MAX_LENGTH = 25;

    /**
     * A merchant reference must match this pattern to be accepted by Qliro.
     */
    const VALIDATE_MERCHANT_REFERENCE = '/^[A-Za-z0-9_-]{1,25}$/';

    /**
     * Resolve a supposedly unique hash for QliroOne order reference.
     * It must be a string of any length, but important to remember that it will be truncated to up to 25 characters max
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return string
     */
    public function resolveHash(CartInterface $quote);
}
