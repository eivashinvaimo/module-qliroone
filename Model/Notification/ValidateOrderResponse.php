<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Notification;

use Qliro\QliroOne\Api\Data\ValidateOrderResponseInterface;

/**
 * Validate Order Response class
 */
class ValidateOrderResponse implements ValidateOrderResponseInterface
{
    /**
     * @var string
     */
    private $declineReason;

    /**
     * @return string
     */
    public function getDeclineReason()
    {
        return $this->declineReason;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDeclineReason($value)
    {
        $this->declineReason = $value;

        return $this;
    }
}
