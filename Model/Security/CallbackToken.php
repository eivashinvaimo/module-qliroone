<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Security;

use Qliro\QliroOne\Model\Config;
use Qliro\QliroOne\Model\Logger\Manager;

/**
 * Notification Callback Token handling class
 */
class CallbackToken
{
    /**
     * @var \Qliro\QliroOne\Model\Security\Jwt
     */
    private $jwt;

    /**
     * @var \Qliro\QliroOne\Model\Config
     */
    private $qliroConfig;

    /**
     * @var \Qliro\QliroOne\Model\Logger\Manager
     */
    private $logManager;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Model\Security\Jwt $jwt
     * @param \Qliro\QliroOne\Model\Config $qliroConfig
     * @param \Qliro\QliroOne\Model\Logger\Manager $logManager
     */
    public function __construct(
        Jwt $jwt,
        Config $qliroConfig,
        Manager $logManager
    ) {
        $this->jwt = $jwt;
        $this->qliroConfig = $qliroConfig;
        $this->logManager = $logManager;
    }

    /**
     * Get a new token that will expire in 2 hours
     *
     * @return string
     */
    public function getToken()
    {
        $payload = [
            'merchant' => $this->qliroConfig->getMerchantApiKey(),
            'expires' => date('Y-m-d H:i:s', $this->getExpirationTimestamp()),
            'additional_data' => $this->getAdditionalData(),
        ];

        return $this->jwt->encode($payload, $this->qliroConfig->getMerchantApiSecret());
    }

    /**
     * Verify if the token is valid
     *
     * @param string $token
     * @return bool
     */
    public function verifyToken($token)
    {
        try {
            $payload = $this->jwt->decode($token, $this->qliroConfig->getMerchantApiSecret(), true);
        } catch (\Exception $exception) {
            return false;
        }

        $merchant = $payload['merchant'] ?? null;
        $expiresAt = isset($payload['expires']) ? strtotime($payload['expires']) : 0;
        $additionalData = $payload['additional_data'] ?? null;

        $this->logManager->setMark('SECURITY TOKEN');
        $this->logManager->addTag('security');

        if ($merchant !== $this->qliroConfig->getMerchantApiKey()) {
            $this->logManager->debug(
                'merchant ID mismatch',
                [
                    'extra' => [
                        'request' => $merchant,
                        'configured' => $this->qliroConfig->getMerchantApiKey()
                    ]
                ]
            );

            $this->logManager->setMark(null);
            $this->logManager->removeTag('security');

            return false;
        }

        if ($additionalData != $this->getAdditionalData()) {
            $this->logManager->debug(
                'additional data mismatch',
                [
                    'extra' => [
                        'additional_data' => $additionalData,
                    ]
                ]
            );

            $this->logManager->setMark(null);
            $this->logManager->removeTag('security');

            return false;
        }

        if ($expiresAt - time() < 0) {
            $this->logManager->debug(
                'expired {expired} seconds ago',
                [
                    'expired' => time() - $expiresAt
                ]
            );

            $this->logManager->setMark(null);
            $this->logManager->removeTag('security');

            return false;
        }

        $this->logManager->setMark(null);
        $this->logManager->removeTag('security');

        return true;
    }

    /**
     * Get an expiration timestamp for the token
     *
     * @return int
     */
    public function getExpirationTimestamp()
    {
        /* Reason from QliroOne API documentation:
         * If the callback is not received by Qliro One, seven retry attempts are scheduled
         * at 30 seconds, 60 seconds, 2 minutes, 30 minutes, 1 hour, 24 hours and 3 days.
         * We also need an additional day for the shopper taking their time before placing the order.
         */
        return strtotime('+4 day');
    }

    /**
     * Get additional data used for modifying security token
     *
     * @return mixed
     */
    public function getAdditionalData()
    {
        return null;
    }
}
