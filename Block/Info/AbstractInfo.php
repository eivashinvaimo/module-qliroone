<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Qliro\QliroOne\Block\Info;

use Qliro\QliroOne\Model\Config;
use Qliro\QliroOne\Api\ApiServiceInterface;

abstract class AbstractInfo extends \Magento\Payment\Block\Info
{
    /**
     * @var boolean
     */
    private $warning;

    /**
     * @var string
     */
    private $warningText;

    /**
     * Fetch received WarningText
     *
     * @return string
     */
    public function getWarningText()
    {
        if ($this->warningText === null) {
            $this->convertAdditionalInformation();
        }
        return $this->warningText;
    }

    /**
     * If a warning is due
     *
     * @return string
     */
    public function showWarning()
    {
        if ($this->warning === null) {
            $this->convertAdditionalInformation();
        }
        return $this->warning;
    }

    /**
     * Takes specific data from AdditionalInformation field and make it available for FE
     * @return $this
     */
    private function convertAdditionalInformation()
    {
        return $this;
    }
}
