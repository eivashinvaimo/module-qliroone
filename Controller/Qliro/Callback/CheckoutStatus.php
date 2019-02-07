<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Controller\Qliro\Callback;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Qliro\QliroOne\Api\Data\CheckoutStatusInterface;
use Qliro\QliroOne\Api\Data\CheckoutStatusResponseInterface;
use Qliro\QliroOne\Api\ManagementInterface;
use Qliro\QliroOne\Helper\Data;
use Qliro\QliroOne\Model\Config;
use Qliro\QliroOne\Model\ContainerMapper;
use Qliro\QliroOne\Model\Security\CallbackToken;
use Qliro\QliroOne\Model\Logger\Manager as LogManager;

/**
 * Checkout status push callback controller action
 */
class CheckoutStatus extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Qliro\QliroOne\Model\Config
     */
    private $qliroConfig;

    /**
     * @var \Qliro\QliroOne\Api\ManagementInterface
     */
    private $qliroManagement;

    /**
     * @var \Qliro\QliroOne\Model\ContainerMapper
     */
    private $containerMapper;

    /**
     * @var \Qliro\QliroOne\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Qliro\QliroOne\Model\Security\CallbackToken
     */
    private $callbackToken;

    /**
     * @var \Qliro\QliroOne\Model\Logger\Manager
     */
    private $logManager;

    /**
     * Inject dependencies
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Qliro\QliroOne\Model\Config $qliroConfig
     * @param \Qliro\QliroOne\Api\ManagementInterface $qliroManagement
     * @param \Qliro\QliroOne\Model\ContainerMapper $containerMapper
     * @param \Qliro\QliroOne\Helper\Data $dataHelper
     * @param \Qliro\QliroOne\Model\Security\CallbackToken $callbackToken
     * @param \Qliro\QliroOne\Model\Logger\Manager $logManager
     */
    public function __construct(
        Context $context,
        Config $qliroConfig,
        ManagementInterface $qliroManagement,
        ContainerMapper $containerMapper,
        Data $dataHelper,
        CallbackToken $callbackToken,
        LogManager $logManager
    ) {
        parent::__construct($context);

        $this->qliroConfig = $qliroConfig;
        $this->qliroManagement = $qliroManagement;
        $this->containerMapper = $containerMapper;
        $this->dataHelper = $dataHelper;
        $this->callbackToken = $callbackToken;
        $this->logManager = $logManager;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $start = \microtime(true);

        if (!$this->qliroConfig->isActive()) {
            return $this->dataHelper->sendPreparedPayload(
                [
                    CheckoutStatusResponseInterface::CALLBACK_RESPONSE =>
                        CheckoutStatusResponseInterface::RESPONSE_NOTIFICATIONS_DISABLED
                ],
                400,
                null,
                'CALLBACK:CHECKOUT_STATUS:ERROR_INACTIVE'
            );
        }

        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->getRequest();

        if (!$this->callbackToken->verifyToken($request->getParam('token'))) {
            return $this->dataHelper->sendPreparedPayload(
                [
                    CheckoutStatusResponseInterface::CALLBACK_RESPONSE =>
                        CheckoutStatusResponseInterface::RESPONSE_AUTHENTICATE_ERROR
                ],
                400,
                null,
                'CALLBACK:CHECKOUT_STATUS:ERROR_TOKEN'
            );
        }

        $payload = $this->dataHelper->readPreparedPayload($request, 'CALLBACK:CHECKOUT_STATUS');

        /** @var \Qliro\QliroOne\Api\Data\CheckoutStatusInterface $updateContainer */
        $updateContainer = $this->containerMapper->fromArray(
            $payload,
            CheckoutStatusInterface::class
        );

        $responseContainer = $this->qliroManagement->checkoutStatus($updateContainer);

        $response = $this->dataHelper->sendPreparedPayload(
            $responseContainer,
            $responseContainer->getCallbackResponse() == CheckoutStatusResponseInterface::RESPONSE_RECEIVED ? 200 : 500,
            null,
            'CALLBACK:CHECKOUT_STATUS'
        );

        $this->logManager->info('result in {duration} seconds', ['duration' => \microtime(true) - $start]);

        return $response;
    }
}
