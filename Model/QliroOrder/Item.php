<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder;

use Qliro\QliroOne\Api\Data\QliroOrderItemInterface;

/**
 * QliroOne order item class
 */
class Item implements QliroOrderItemInterface
{
    /**
     * @var string
     */
    private $merchantReference;

    /**
     * Get item type.
     * Can be 'Product', 'Discount', 'Fee' or 'Shipping'
     *
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var float
     */
    private $pricePerItemIncVat;

    /**
     * @var float
     */
    private $pricePerItemExVat;

    /**
     * @var string
     */
    private $description;

    /**
     * @var array
     */
    private $metaData;

    /**
     * Getter.
     *
     * @return string
     */
    public function getMerchantReference()
    {
        return $this->merchantReference;
    }

    /**
     * @param string $merchantReference
     * @return Item
     */
    public function setMerchantReference($merchantReference)
    {
        $this->merchantReference = $merchantReference;

        return $this;
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Item
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Getter.
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return Item
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Getter.
     *
     * @return float
     */
    public function getPricePerItemIncVat()
    {
        return $this->pricePerItemIncVat;
    }

    /**
     * @param float $pricePerItemIncVat
     * @return Item
     */
    public function setPricePerItemIncVat($pricePerItemIncVat)
    {
        $this->pricePerItemIncVat = $pricePerItemIncVat;

        return $this;
    }

    /**
     * Getter.
     *
     * @return float
     */
    public function getPricePerItemExVat()
    {
        return $this->pricePerItemExVat;
    }

    /**
     * @param float $pricePerItemExVat
     * @return Item
     */
    public function setPricePerItemExVat($pricePerItemExVat)
    {
        $this->pricePerItemExVat = $pricePerItemExVat;

        return $this;
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Item
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Getter.
     *
     * @return array
     */
    public function getMetaData()
    {
        return $this->metaData;
    }

    /**
     * @param array $metaData
     * @return Item
     */
    public function setMetaData($metaData)
    {
        $this->metaData = $metaData;

        return $this;
    }
}
