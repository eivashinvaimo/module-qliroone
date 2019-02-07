<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\GeoIp;

use Qliro\QliroOne\Api\GeoIpResolverInterface;

/**
 * Default GeoIp Resolver class
 */
class DefaultResolver implements GeoIpResolverInterface
{
    /**
     * Resolve country code from IP address
     *
     * @param string $ipAddress
     * @return string|null
     */
    public function getCountryCode($ipAddress)
    {
        if (extension_loaded('geoip')) {
            return \geoip_country_code_by_name($ipAddress);
        }

        return null;
    }
}
