<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api;

/**
 * GeoIp Resolver interface
 */
interface GeoIpResolverInterface
{
    /**
     * Resolve country from IP address
     *
     * @param string $ipAddress
     * @return string|null
     */
    public function getCountryCode($ipAddress);
}
