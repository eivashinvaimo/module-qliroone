<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * API type source config model, returns a list of PROD and SANDBOX
 */
class ApiType implements ArrayInterface
{
    /**
     * Get a list of options for API types
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = [
            ['value' => 'sandbox', 'label' => __('Sandbox')],
            ['value' => 'prod', 'label' => __('Production')],
        ];

        return $result;
    }
}
