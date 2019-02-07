<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Security;

use Magento\Framework\Serialize\Serializer\Json;

/**
 * JWT-compatible token handling class
 */
class Jwt
{
    /**
     * @var array
     */
    private $supportedMethods = [
        'HS256' => 'sha256',
        'HS384' => 'sha384',
        'HS512' => 'sha512',
    ];

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $json;

    /**
     * Inject dependencies
     *
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     */
    public function __construct(
        Json $json
    ) {
        $this->json = $json;
    }

    /**
     * Decode a JWT string into an array
     *
     * @param string $jwt
     * @param string|null $secretKey
     * @param bool $verify Don't skip verification process
     * @return array
     * @throws \UnexpectedValueException
     * @throws \DomainException
     */
    public function decode($jwt, $secretKey = null, $verify = true)
    {
        $tokenSegments = explode('.', $jwt);

        if (count($tokenSegments) != 3) {
            throw new \UnexpectedValueException('Wrong number of segments.');
        }

        list($encodedHead, $encodedBody, $encryption) = $tokenSegments;

        if (null === ($header = $this->jsonDecode($this->safeDecode($encodedHead)))) {
            throw new \UnexpectedValueException('Invalid segment encoding.');
        }

        if (null === $payload = $this->jsonDecode($this->safeDecode($encodedBody))) {
            throw new \UnexpectedValueException('Invalid segment encoding.');
        }

        $sig = $this->safeDecode($encryption);

        if ($verify) {
            if (empty($header['alg'])) {
                throw new \DomainException('Empty algorithm');
            }

            if ($sig != $this->sign("$encodedHead.$encodedBody", $secretKey, $header['alg'])) {
                throw new \UnexpectedValueException('Signature verification failed.');
            }
        }

        return $payload;
    }

    /**
     * Convert and sign a payload into a JWT string
     *
     * @param array $payload
     * @param string $secretKey
     * @param string $algorithm
     * @return string
     */
    public function encode($payload, $secretKey, $algorithm = 'HS256')
    {
        $header = ['typ' => 'JWT', 'alg' => $algorithm];
        $segments = [];
        $segments[] = $this->safeEncode($this->jsonEncode($header));
        $segments[] = $this->safeEncode($this->jsonEncode($payload));
        $signingInput = implode('.', $segments);
        $signature = $this->sign($signingInput, $secretKey, $algorithm);
        $segments[] = $this->safeEncode($signature);

        return implode('.', $segments);
    }

    /**
     * Sign a string with a given key and algorithm
     *
     * @param string $message
     * @param string $secretKey
     * @param string $algorithm
     * @return string
     * @throws \DomainException
     */
    private function sign($message, $secretKey, $algorithm = 'HS256')
    {
        if (!isset($this->supportedMethods[$algorithm])) {
            throw new \DomainException('Algorithm is not supported.');
        }

        return \hash_hmac($this->supportedMethods[$algorithm], $message, $secretKey, true);
    }

    /**
     * Decode a JSON string into a PHP object.
     *
     * @param string $input JSON string
     * @return array
     * @throws \DomainException Provided string was invalid JSON
     */
    private function jsonDecode($input)
    {
        try {
            $data = $this->json->unserialize($input);
        } catch (\InvalidArgumentException $exception) {
            throw new \DomainException('Unknown JSON decode error.');
        }

        if ($data === null && $input !== 'null') {
            throw new \DomainException('Null result with non-null input.');
        }

        return $data;
    }

    /**
     * Encode an array into a JSON string
     *
     * @param array $input
     * @return string
     * @throws \DomainException
     */
    private function jsonEncode($input)
    {
        try {
            $json = $this->json->serialize($input);
        } catch (\InvalidArgumentException $exception) {
            throw new \DomainException('Unknown JSON encode error.');
        }

        if ($json === 'null' && $input !== null) {
            throw new \DomainException('Null result with non-null input');
        }

        return $json;
    }

    /**
     * Decode a string with URL-safe Base64
     *
     * @param string $input
     * @return string
     */
    private function safeDecode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }

        return \base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * Encode a string with URL-safe Base64
     *
     * @param string $input
     * @return string
     */
    private function safeEncode($input)
    {
        return str_replace('=', '', strtr(\base64_encode($input), '+/', '-_'));
    }
}
