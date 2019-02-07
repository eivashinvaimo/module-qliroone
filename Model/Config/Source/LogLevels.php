<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Config\Source;

use Monolog\Logger;
use Magento\Framework\Option\ArrayInterface;

/**
 * Log Levels source model class.
 * Provide UI for choosing what log messages should be recorded
 */
class LogLevels implements ArrayInterface
{
    /** @var Logger */
    private $logger;

    public function __construct(
        Logger $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [['value' => 0, 'label' => 'No Logging']];
        foreach ($this->logger->getLevels() as $label => $value) {
            $result[] = ['value' => $value, 'label' => ucwords(strtolower($label))];
        }

        return $result;
    }
}
