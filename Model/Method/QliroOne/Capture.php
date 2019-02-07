<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Qliro\QliroOne\Model\Method\QliroOne;

use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Command\ResultInterface;

/**
 * Class Capture for QliroOne payment method
 */
class Capture implements CommandInterface
{
    /**
     * @var \Qliro\QliroOne\Model\Management
     */
    private $management;

    /**
     * @var \Qliro\QliroOne\Model\Config
     */
    private $qliroConfig;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Model\Management $management
     * @param \Qliro\QliroOne\Model\Config $qliroConfig
     */
    public function __construct(
        \Qliro\QliroOne\Model\Management $management,
        \Qliro\QliroOne\Model\Config $qliroConfig
    ) {
        $this->management = $management;
        $this->qliroConfig = $qliroConfig;
    }

    /**
     * Capture command
     *
     * @param array $commandSubject
     *
     * @return ResultInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(array $commandSubject)
    {
        /** @var \Magento\Payment\Model\InfoInterface $payment */
        $payment = $commandSubject['payment']->getPayment();
        $amount = $commandSubject['amount'];

        try {
            if ($this->qliroConfig->shouldCaptureOnInvoice()) {
                $this->management->captureByInvoice($payment, $amount);
            }
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Unable to capture payment for this order.')
            );
        }

        return $this;
    }
}
