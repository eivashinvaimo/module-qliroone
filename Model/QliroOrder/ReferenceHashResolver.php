<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

// @codingStandardsIgnoreFile
// phpcs:ignoreFile

namespace Qliro\QliroOne\Model\QliroOrder;

use Magento\Quote\Api\Data\CartInterface;
use Qliro\QliroOne\Api\HashResolverInterface;

/**
 * QliroOne order reference hash resolver class
 */
class ReferenceHashResolver implements HashResolverInterface
{
    const CHARSET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * Resolve a supposedly unique hash for QliroOne order reference.
     * It must be a string of any length, but important to remember that it will be truncated to up to 25 characters max
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return string
     */
    public function resolveHash(CartInterface $quote)
    {
        srand();
        $result = '';
        for ($index = 0; $index < self::HASH_MAX_LENGTH; ++$index) {
            $result .= self::CHARSET[rand(0, strlen(self::CHARSET) - 1)];
        }

        return $result;
    }
}
