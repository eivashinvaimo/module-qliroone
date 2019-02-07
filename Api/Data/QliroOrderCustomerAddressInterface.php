<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * QliroOne Order CustomerInfo Address interface
 *
 * @api
 */
interface QliroOrderCustomerAddressInterface extends ContainerInterface
{
    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @return string
     */
    public function getCareOf();

    /**
     * @return string
     */
    public function getCompanyName();

    /**
     * @return string
     */
    public function getStreet();

    /**
     * @return string
     */
    public function getPostalCode();

    /**
     * @return string
     */
    public function getCity();

    /**
     * @param string $value
     * @return $this
     */
    public function setFirstName($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setLastName($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCareOf($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCompanyName($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setStreet($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setPostalCode($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCity($value);
}
