<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Api\Client;

use GuzzleHttp\Exception\RequestException;
use Magento\Framework\Serialize\Serializer\Json;
use Qliro\QliroOne\Api\Data\QliroOrderCreateRequestInterface;
use Qliro\QliroOne\Api\Data\QliroOrderInterface;
use Qliro\QliroOne\Api\Data\QliroOrderInterfaceFactory;
use Qliro\QliroOne\Api\Data\QliroOrderUpdateRequestInterface;
use Qliro\QliroOne\Model\Api\Client\Exception\MerchantApiException;
use Qliro\QliroOne\Model\Api\Client\Exception\ClientException;
use Qliro\QliroOne\Model\Api\Service;
use Qliro\QliroOne\Model\Config;
use Qliro\QliroOne\Model\ContainerMapper;
use Qliro\QliroOne\Model\Exception\TerminalException;
use Qliro\QliroOne\Model\Logger\Manager as LogManager;

/**
 * Merchant API client class
 */
class Merchant implements \Qliro\QliroOne\Api\Client\MerchantInterface
{
    /**
     * @var \Qliro\QliroOne\Model\Api\Service
     */
    private $service;

    /**
     * @var \Qliro\QliroOne\Model\Config
     */
    private $config;

    /**
     * @var \Qliro\QliroOne\Model\ContainerMapper
     */
    private $containerMapper;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $json;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderInterfaceFactory
     */
    private $qliroOrderFactory;

    /**
     * @var \Qliro\QliroOne\Model\Logger\Manager
     */
    private $logManager;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Model\Api\Service $service
     * @param \Qliro\QliroOne\Model\Config $config
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Qliro\QliroOne\Model\ContainerMapper $containerMapper
     * @param \Qliro\QliroOne\Api\Data\QliroOrderInterfaceFactory $qliroOrderFactory
     * @param \Qliro\QliroOne\Model\Logger\Manager $logManager
     */
    public function __construct(
        Service $service,
        Config $config,
        Json $json,
        ContainerMapper $containerMapper,
        QliroOrderInterfaceFactory $qliroOrderFactory,
        LogManager $logManager
    ) {
        $this->service = $service;
        $this->config = $config;
        $this->containerMapper = $containerMapper;
        $this->json = $json;
        $this->qliroOrderFactory = $qliroOrderFactory;
        $this->logManager = $logManager;
    }

    /**
     * Perform QliroOne order creation
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderCreateRequestInterface $qliroOrderCreateRequest
     * @return int
     * @throws \Qliro\QliroOne\Model\Api\Client\Exception\ClientException
     */
    public function createOrder(QliroOrderCreateRequestInterface $qliroOrderCreateRequest)
    {
        $this->logManager->addTag('sensitive');

        $qliroOrderId = null;
        $payload = $this->containerMapper->toArray($qliroOrderCreateRequest);

        try {
            $response = $this->service->post('checkout/merchantapi/orders', $payload);
            $qliroOrderId = $response['OrderId'] ?? null;
        } catch (\Exception $exception) {
            $this->logManager->removeTag('sensitive');
            $this->handleExceptions($exception);
        }

        $this->logManager->removeTag('sensitive');

        return $qliroOrderId;
    }

    /**
     * Get QliroOne order by its Qliro Order ID
     *
     * @param int $qliroOrderId
     * @return \Qliro\QliroOne\Api\Data\QliroOrderInterface
     * @throws \Qliro\QliroOne\Model\Api\Client\Exception\ClientException
     */
    public function getOrder($qliroOrderId)
    {
        $this->logManager->addTag('sensitive');

        /** @var QliroOrderInterface $qliroOrder */
        $qliroOrder = $this->qliroOrderFactory->create();

        try {
            $response = $this->service->get('checkout/merchantapi/orders/{OrderId}', ['OrderId' => $qliroOrderId]);
            $this->containerMapper->fromArray($response, $qliroOrder);
        } catch (\Exception $exception) {
            $this->logManager->removeTag('sensitive');
            $this->handleExceptions($exception);
        }

        $this->logManager->removeTag('sensitive');

        return $qliroOrder;
    }

    /**
     * Update QliroOne order
     *
     * @param int $qliroOrderId
     * @param \Qliro\QliroOne\Api\Data\QliroOrderUpdateRequestInterface $qliroOrderUpdateRequest
     * @return int
     * @throws \Qliro\QliroOne\Model\Api\Client\Exception\ClientException
     */
    public function updateOrder($qliroOrderId, QliroOrderUpdateRequestInterface $qliroOrderUpdateRequest)
    {
        $this->logManager->addTag('sensitive');

        $payload = $this->containerMapper->toArray($qliroOrderUpdateRequest);
        $payload['OrderId'] = $qliroOrderId;

        try {
            $response = $this->service->put('checkout/merchantapi/orders/{OrderId}', $payload);
        } catch (\Exception $exception) {
            $this->logManager->removeTag('sensitive');
            $this->handleExceptions($exception);
        }

        $this->logManager->removeTag('sensitive');

        return $qliroOrderId;
    }

    /**
     * Handle exceptions that come from the API response
     *
     * @param \Exception $exception
     * @throws \Qliro\QliroOne\Model\Api\Client\Exception\ClientException
     */
    private function handleExceptions(\Exception $exception)
    {
        if ($exception instanceof RequestException) {
            $data = $this->json->unserialize($exception->getResponse()->getBody());

            if (isset($data['ErrorCode']) && isset($data['ErrorMessage'])) {
                if (!($exception instanceof TerminalException)) {
                    $this->logManager->critical($exception, ['extra' => $data]);
                }

                throw new MerchantApiException(
                    __('Error [%1]: %2', $data['ErrorCode'], $data['ErrorMessage'])
                );
            }
        }

        if (!($exception instanceof TerminalException)) {
            $this->logManager->critical($exception);
        }

        throw new ClientException(__('Request to Qliro One has failed.'), $exception);
    }
}
