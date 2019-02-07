<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Qliro\QliroOne\Model\Method\QliroOne;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\CommandInterface;
use Qliro\QliroOne\Api\LinkRepositoryInterface;
use Qliro\QliroOne\Api\ManagementInterface;
use Qliro\QliroOne\Model\Exception\LinkInactiveException;
use Qliro\QliroOne\Model\Exception\TerminalException;
use Qliro\QliroOne\Model\Logger\Manager as LogManager;

/**
 * Class Capture for QliroOne payment method
 */
class Cancel implements CommandInterface
{
    /**
     * @var \Qliro\QliroOne\Api\LinkRepositoryInterface
     */
    private $linkRepository;

    /**
     * @var \Qliro\QliroOne\Model\Logger\Manager
     */
    private $logManager;

    /**
     * @var \Qliro\QliroOne\Api\ManagementInterface
     */
    private $management;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Api\LinkRepositoryInterface $linkRepository
     * @param \Qliro\QliroOne\Model\Logger\Manager $logManager
     * @param \Qliro\QliroOne\Api\ManagementInterface $management
     */
    public function __construct(
        LinkRepositoryInterface $linkRepository,
        LogManager $logManager,
        ManagementInterface $management
    ) {
        $this->linkRepository = $linkRepository;
        $this->logManager = $logManager;
        $this->management = $management;
    }

    /**
     * Cancel command
     *
     * @param array $commandSubject
     * @return null
     * @throws \Exception
     */
    public function execute(array $commandSubject)
    {
        if (isset($commandSubject['payment'])) {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $commandSubject['payment']->getOrder();
            $orderId = $order->getId();
        } else {
            $orderId = null;
        }

        try {
            try {
                $link = $this->linkRepository->getByOrderId($orderId);
            } catch (NoSuchEntityException $exception) {
                $this->linkRepository->getByOrderId($orderId, false);
                throw new LinkInactiveException('This order has already been processed and the link deactivated.');
            }

            $this->logManager->setMerchantReference($link->getReference());

            $link->setIsActive(false);
            $link->setMessage(sprintf('Order #%s marked as canceled', $orderId));
            $this->linkRepository->save($link);

            $this->management->cancelQliroOrder($link->getQliroOrderId());
            $this->logManager->info(
                'Canceled order, requested a QliroOne order cancellation',
                [
                    'extra' => [
                        'order_id' => $orderId,
                        'qliro_order_id' => $link->getQliroOrderId(),
                    ]
                ]
            );
        } catch (LinkInactiveException $exception) {
            return null;
        } catch (\Exception $exception) {
            $logData = [
                'order_id' => $orderId,
                'qliro_order_id' => isset($link) ? $link->getQliroOrderId() : null,
            ];

            if (!($exception instanceof TerminalException)) {
                $this->logManager->critical($exception, ['extra' => $logData]);

                throw $exception;
            }

            $this->logManager->debug('Cancellation was unsuccessful.', ['extra' => $logData]);
        }

        return null;
    }
}
