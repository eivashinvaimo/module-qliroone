<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder;

use Qliro\QliroOne\Api\Data\QliroOrderIdentityVerificationInterface;

/**
 * IdentityVerification class
 */
class IdentityVerification implements QliroOrderIdentityVerificationInterface
{
    /**
     * @var bool
     */
    private $identityVerified;

    /**
     * @var bool
     */
    private $requireIdentityVerification;

    /**
     * @return bool
     */
    public function getRequireIdentityVerification()
    {
        return $this->requireIdentityVerification;
    }

    /**
     * @return bool
     */
    public function getIdentityVerified()
    {
        return $this->identityVerified;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setRequireIdentityVerification($value)
    {
        $this->requireIdentityVerification = $value;

        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setIdentityVerified($value)
    {
        $this->identityVerified = $value;

        return $this;
    }
}
