<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * QliroOne Order Item interface
 *
 * Depending on the data provided when sending an order item, Qliro One will interpret the type and price according to the table below.
 *
 * +----------------------+-----------------+-----------------+----------------------+-------------------------+------------------------+
 * | Type                 | PriceIncVat     | PriceExVat      | Interpreted Type     | Interpreted PriceIncVat | Interpreted PriceExVat |
 * +----------------------+-----------------+-----------------+----------------------+-------------------------+------------------------+
 * | null                 | Positive (X>=0) | Positive (Y>=0) | Product              | X                       | Y                      |
 * | null                 | Negative (X<0)  | Negative (Y<0)  | Discount             | X                       | Y                      |
 * | Product/Fee/Shipping | X               | Y               | Product/Fee/Shipping | Abs(X)                  | Abs(Y)                 |
 * | Discount             | X               | Y               | Discount             | -Abs(X)                 | -Abs(Y)                |
 * +----------------------+-----------------+-----------------+----------------------+-------------------------+------------------------+
 *
 * @api
 */
interface QliroOrderItemInterface extends ContainerInterface
{
    const TYPE_PRODUCT = 'Product';
    const TYPE_DISCOUNT = 'Discount';
    const TYPE_FEE = 'Fee';
    const TYPE_SHIPPING = 'Shipping';

    /**
     * @return string
     */
    public function getMerchantReference();

    /**
     * Get item type.
     * Can be 'Product', 'Discount', 'Fee' or 'Shipping'
     *
     * @return string
     */
    public function getType();

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @return float
     */
    public function getPricePerItemIncVat();

    /**
     * @return float
     */
    public function getPricePerItemExVat();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return array
     */
    public function getMetaData();

    /**
     * @param string $value
     * @return $this
     */
    public function setMerchantReference($value);

    /**
     * Set item type.
     * Can be 'Product', 'Discount', 'Fee' or 'Shipping'
     *
     * @param string $value
     * @return $this
     */
    public function setType($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setQuantity($value);

    /**
     * @param float $value
     * @return $this
     */
    public function setPricePerItemIncVat($value);

    /**
     * @param float $value
     * @return $this
     */
    public function setPricePerItemExVat($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setDescription($value);

    /**
     * Additional metadata.
     *
     * In OrderManagement API can be used to have two possible elements
     * - HeaderLines (array) Array of strings that will be diplayed above the item on the invoice.
     *   Maximum number of strings is 5 and maximum length of each string is 115 characters.
     * - FooterLines (array) Array of strings that will be diplayed below the item on the invoice.
     *   Maximum number of strings is 5 and maximum length of each string is 115 characters.
     *
     * @param array $value
     * @return $this
     */
    public function setMetaData($value);
}
