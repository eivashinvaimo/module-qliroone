<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Api;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Qliro\QliroOne\Model\Config;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;
use Magento\Framework\Serialize\Serializer\Json;
use Qliro\QliroOne\Model\Exception\TerminalException;
use Qliro\QliroOne\Model\Logger\Manager;

/**
 * QliroOne API Service implementation
 */
class Service implements \Qliro\QliroOne\Api\ApiServiceInterface
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';

    const HEADER_CONTENT_TYPE = 'Content-Type';
    const HEADER_CONTENT_TYPE_JSON = 'application/json';
    const AUTHENTICATION_PREFIX = 'Qliro';
    const HEADER_AUTHENTICATION = 'Authorization';
    const QLIRO_SANDBOX_API_URL = 'https://pago.qit.nu';
    const QLIRO_PROD_API_URL = 'https://payments.qit.nu';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $json;

    /**
     * @var \Qliro\QliroOne\Model\Logger\Manager
     */
    private $logManager;

    /**
     * @var float
     */
    private $duration;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Model\Config $config
     * @param \GuzzleHttp\Client $client
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Qliro\QliroOne\Model\Logger\Manager $logManager
     */
    public function __construct(
        Config $config,
        Client $client,
        Json $json,
        Manager $logManager
    ) {
        $this->config = $config;
        $this->client = $client;
        $this->json = $json;
        $this->logManager = $logManager;
    }

    /**
     * Perform GET request
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws \InvalidArgumentException
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     */
    public function get($endpoint, $data = [])
    {
        $this->applyParams($endpoint, $data);

        return $this->call(self::METHOD_GET, $endpoint, $data);
    }

    /**
     * Perform POST request
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws \InvalidArgumentException
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     */
    public function post($endpoint, $data = [])
    {
        return $this->call(self::METHOD_POST, $endpoint, $data);
    }

    /**
     * Perform PUT request
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws \InvalidArgumentException
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     */
    public function put($endpoint, $data = [])
    {
        $this->applyParams($endpoint, $data);

        return $this->call(self::METHOD_PUT, $endpoint, $data);
    }

    /**
     * Replace all placeholders within endpoint from the $params array
     *
     * @param string $endpoint
     * @param array $params
     */
    private function applyParams(&$endpoint, &$params)
    {
        foreach ($params as $key => $value) {
            if (!is_scalar($value)) {
                continue;
            }
            $modifiedEndpoint = str_replace('{' . $key . '}', $value, $endpoint);

            if ($modifiedEndpoint !== $endpoint) {
                unset($params[$key]);
                $endpoint = $modifiedEndpoint;
            }
        }

        $endpoint = preg_replace('/\{[^}]+\}/', '*', $endpoint);
    }

    /**
     * Perform an API call
     *
     * @param string $method
     * @param string $endpoint
     * @param array $body
     * @return array
     * @throws \InvalidArgumentException
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     */
    private function call($method, $endpoint, $body = [])
    {
        $this->logManager->setMark('REST API');

        if ($method === self::METHOD_GET) {
            $payload = '';
            $options[RequestOptions::QUERY] = $body;
        } else {
            $body['MerchantApiKey'] = $this->config->getMerchantApiKey();
            $payload = $this->json->serialize($body);
            $options[RequestOptions::BODY] = $payload;
        }

        $headers = [
            self::HEADER_CONTENT_TYPE => self::HEADER_CONTENT_TYPE_JSON,
            self::HEADER_AUTHENTICATION => $this->getAuthenticationToken($payload, $method)
        ];

        $options[RequestOptions::HEADERS] = $headers;
        $options[RequestOptions::ON_STATS] = [$this, 'receiveStats'];

        $this->duration = 0.0;
        $endpointUri = $this->prepareEndpointUri($endpoint);

        $this->logManager->debug(
            '>>> {method} {endpoint}',
            [
                'method' => $method,
                'endpoint' => $endpoint,
                'extra' => [
                    'uri' => $endpointUri,
                    'body' => $body,
                ],
            ]
        );

        try {
            $response = $this->client->request($method, $endpointUri, $options);
            $responseData = $this->getResponseData($response);

            $this->logManager->debug(
                '<<< Result in {duration} seconds',
                [
                    'duration' => $this->duration,
                    'extra' => [
                        'uri' => $endpointUri,
                        'request' => $body,
                        'status_code' => $response->getStatusCode(),
                        'response' => $responseData,
                    ]
                ]
            );
        } catch (\Exception $exception) {
            $exceptionData = [
                'exception' => $exception->getMessage(),
                'uri' => $endpointUri,
                'request' => $body,
            ];

            if ($exception instanceof ClientException) {
                $response = $exception->getResponse();

                $exceptionData = array_merge($exceptionData, [
                    'status_code' => $response->getStatusCode(),
                    'error_reason' => $response->getReasonPhrase(),
                    'response' => $this->getResponseData($response),
                ]);
            }

            $this->logManager->error(
                '<<< Exception after {duration} seconds',
                [
                    'duration' => $this->duration,
                    'extra' => $exceptionData
                ]
            );

            throw new TerminalException($exception->getMessage(), $exception->getCode(), $exception);
        } finally {
            $this->logManager->setMark(null);
        }

        return $responseData;
    }

    /**
     * @param \GuzzleHttp\TransferStats $stats
     */
    public function receiveStats(TransferStats $stats)
    {
        $this->duration = $stats->getTransferTime();
    }

    /**
     * Prepare a full URI to the endpoint
     *
     * @param string $endpoint
     * @return string
     */
    private function prepareEndpointUri($endpoint)
    {
        $baseUri = $this->config->getApiType() === 'prod' ? self::QLIRO_PROD_API_URL : self::QLIRO_SANDBOX_API_URL;

        return implode('/', [$baseUri, trim($endpoint, '/')]);
    }

    /**
     * @param string $body
     * @param string $method
     * @return string
     */
    private function getAuthenticationToken($body, $method = self::METHOD_POST)
    {
        if ($method === self::METHOD_GET) {
            $body = '';
        }

        $secret = $this->config->getMerchantApiSecret();
        $secretString = base64_encode(hash('sha256', $body . $secret, true));
        $token = trim(implode(' ', [self::AUTHENTICATION_PREFIX, $secretString]));

        return $token;
    }

    /**
     * Get and decode request data
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return array
     */
    private function getResponseData(ResponseInterface $response): array
    {
        $responseString = (string)$response->getBody();

        try {
            $responseData = $responseString ? (array)$this->json->unserialize($responseString) : [];
        } catch (\InvalidArgumentException $exception) {
            $responseData = [];
        }

        return $responseData;
    }
}
