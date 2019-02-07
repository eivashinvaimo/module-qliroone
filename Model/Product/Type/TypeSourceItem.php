<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Product\Type;

use Qliro\QliroOne\Api\Product\TypeSourceItemInterface;

/**
 * Type Source Item class.
 * Used for mapping Quote, Invoice and other items.
 */
class TypeSourceItem implements TypeSourceItemInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $sku;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * @var float
     */
    private $qty;

    /**
     * @var float
     */
    private $priceInclTax;

    /**
     * @var float
     */
    private $priceExclTax;

    /**
     * @var mixed
     */
    private $item;

    /**
     * @var \Qliro\QliroOne\Api\Product\TypeSourceItemInterface
     */
    private $parent;

    /**
     * Getter.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     * @return $this
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

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
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Getter.
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Getter.
     *
     * @return float
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * @param float $qty
     * @return $this
     */
    public function setQty($qty)
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * Getter.
     *
     * @return float
     */
    public function getPriceInclTax()
    {
        return $this->priceInclTax;
    }

    /**
     * @param float $priceInclTax
     * @return $this
     */
    public function setPriceInclTax($priceInclTax)
    {
        $this->priceInclTax = $priceInclTax;

        return $this;
    }

    /**
     * Getter.
     *
     * @return float
     */
    public function getPriceExclTax()
    {
        return $this->priceExclTax;
    }

    /**
     * @param float $priceExclTax
     * @return $this
     */
    public function setPriceExclTax($priceExclTax)
    {
        $this->priceExclTax = $priceExclTax;

        return $this;
    }

    /**
     * Getter.
     *
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param mixed $item
     * @return $this
     */
    public function setItem($item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @return \Qliro\QliroOne\Api\Product\TypeSourceItemInterface|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param \Qliro\QliroOne\Api\Product\TypeSourceItemInterface $value
     * @return $this
     */
    public function setParent($value)
    {
        $this->parent = $value;

        return $this;
    }
}
