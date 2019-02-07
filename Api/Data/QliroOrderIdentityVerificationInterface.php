<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * QliroOrderIdentityVerificationInterface interface
 *
 * @api
 */
interface QliroOrderIdentityVerificationInterface extends ContainerInterface
{
    /**
     * @return bool
     */
    public function getRequireIdentityVerification();

    /**
     * @return bool
     */
    public function getIdentityVerified();

    /**
     * @param bool $value
     * @return $this
     */
    public function setRequireIdentityVerification($value);

    /**
     * @param bool $value
     * @return $this
     */
    public function setIdentityVerified($value);
}
