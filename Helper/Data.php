<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Qliro\QliroOne\Api\Data\ContainerInterface;
use Qliro\QliroOne\Model\ContainerMapper;
use Qliro\QliroOne\Model\Logger\Manager as LogManager;
use \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

/**
 * QliroOne Data helper class
 */
class Data extends AbstractHelper
{
    /**
     * @var \Qliro\QliroOne\Model\ContainerMapper
     */
    private $containerMapper;

    /**
     * @var \Qliro\QliroOne\Model\Logger\Manager
     */
    private $logManager;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $json;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    private $resultFactory;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * Inject dependencies
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Qliro\QliroOne\Model\ContainerMapper $containerMapper
     * @param \Qliro\QliroOne\Model\Logger\Manager $logManager
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     */
    public function __construct(
        Context $context,
        ContainerMapper $containerMapper,
        LogManager $logManager,
        Json $json,
        ResultFactory $resultFactory,
        RemoteAddress $remoteAddress
    ) {
        parent::__construct($context);
        $this->containerMapper = $containerMapper;
        $this->logManager = $logManager;
        $this->json = $json;
        $this->resultFactory = $resultFactory;
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * Read payload from request, log and prepare
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param string $loggerMark
     * @return array
     */
    public function readPreparedPayload(Http $request, $loggerMark = null)
    {
        $content = $request->getContent();
        $this->logManager->setMark($loggerMark);
        $payload = [];

        $data = [
            'uri' => $request->getRequestUri(),
            'method' => $request->getMethod(),
        ];

        try {
            $payload = $this->json->unserialize($content);
            if (!empty($payload['MerchantReference'])) {
                $this->logManager->setMerchantReference($payload['MerchantReference']);
            }
            $data['body'] = $payload;
        } catch (\InvalidArgumentException $exception) {
            $data['raw_body'] = $content;
            $data['exception'] = $exception->getMessage();
        }

        $this->logManager->debug(
            '<<< JSON payload has been received and processed.',
            [
                'extra' => [
                    'payload' => $data,
                ],
            ]
        );

        $this->logManager->setMark(null);

        return $payload;
    }

    /**
     * Prepare data for payload, log it for debugging and return in a result object
     *
     * @param string|array|\Qliro\QliroOne\Api\Data\ContainerInterface $payload
     * @param int $resultCode
     * @param \Magento\Framework\Controller\Result\Json $resultJson
     * @param string $loggerMark
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function sendPreparedPayload($payload, $resultCode = 200, JsonResult $resultJson = null, $loggerMark = null)
    {
        if (!($resultJson instanceof JsonResult)) {
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        }

        $data = [
            'status_code' => $resultCode,
        ];

        $this->logManager->setMark($loggerMark);
        $resultJson->setHttpResponseCode($resultCode);

        if ($payload instanceof ContainerInterface) {
            $payload = $this->containerMapper->toArray($payload);
            $data['payload'] = $payload;
        } elseif (is_string($payload)) {
            try {
                $payload = $this->json->unserialize($payload);
                $data['payload'] = $payload;
            } catch (\InvalidArgumentException $exception) {
                $data['exception'] =  $exception->getMessage();
            }
        } else {
            $data['payload'] = $payload;
        }

        $this->logManager->debug('>>> Payload was prepared and sent in JSON response.', ['extra' => $data]);

        $this->logManager->setMark(null);
        $resultJson->setData($payload);

        return $resultJson;
    }

    /**
     * Format price to the format compatible with QliroOne API
     *
     * @param float $value
     * @return string
     */
    public function formatPrice($value)
    {
        return \number_format($value, 2, '.', false);
    }

    /**
     * Get current process PID
     *
     * @return int
     */
    public function getPid()
    {
        return \getmypid();
    }

    /**
     * Get client ip
     *
     * @return string
     */
    public function getRemoteIp()
    {
        return $this->remoteAddress->getRemoteAddress();
    }

    /**
     * Check if the process specified by its PID is alive.
     * It works only for the processes of the same user.
     *
     * @param int $pid
     * @return bool
     */
    public function isProcessAlive($pid)
    {
        if (!is_numeric($pid)) {
            return false;
        }
        $pid = \intval($pid);
        if (\function_exists('posix_getpgid')) {
            return \posix_getpgid($pid) !== FALSE;
        } elseif (\function_exists('posix_kill')) {
            return \posix_kill($pid, 0);
        } elseif (defined('PHP_OS') && PHP_OS == 'Linux') {
            return \file_exists("/proc/$pid");
        } else {
            return \shell_exec(sprintf('ps -p %s | wc -l', $pid )) > 1;
        }
    }

    /**
     * Check if two quote addresses match
     *
     * @param \Magento\Quote\Model\Quote\Address $address1
     * @param \Magento\Quote\Model\Quote\Address $address2
     * @return bool
     */
    public function doAddressesMatch($address1, $address2)
    {
        $addressData1 = [
            'email' => $address1->getEmail(),
            'firstname' => $address1->getFirstname(),
            'lastname' => $address1->getLastname(),
            'care_of' => $address1->getCareOf(),
            'company' => $address1->getCompany(),
            'street' => $address1->getStreetFull(),
            'city' => $address1->getCity(),
            'region' => $address1->getRegion(),
            'region_id' => $address1->getRegionId(),
            'postcode' => $address1->getPostcode(),
            'country_id' => $address1->getCountryId(),
            'telephone' => $address1->getTelephone(),
        ];

        $addressData2 = [
            'email' => $address2->getEmail(),
            'firstname' => $address2->getFirstname(),
            'lastname' => $address2->getLastname(),
            'care_of' => $address2->getCareOf(),
            'company' => $address2->getCompany(),
            'street' => $address2->getStreetFull(),
            'city' => $address2->getCity(),
            'region' => $address2->getRegion(),
            'region_id' => $address2->getRegionId(),
            'postcode' => $address2->getPostcode(),
            'country_id' => $address2->getCountryId(),
            'telephone' => $address2->getTelephone(),
        ];

        return \json_encode($addressData1) == \json_encode($addressData2);
    }
}
