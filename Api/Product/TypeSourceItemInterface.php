<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Product;

/**
 * Type Source Item interface
 */
interface TypeSourceItemInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getSku();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct();

    /**
     * @return float
     */
    public function getQty();

    /**
     * @return float
     */
    public function getPriceInclTax();

    /**
     * @return float
     */
    public function getPriceExclTax();

    /**
     * @return mixed
     */
    public function getItem();

    /**
     * @return \Qliro\QliroOne\Api\Product\TypeSourceItemInterface|null
     */
    public function getParent();

    /**
     * @param int $value
     * @return $this
     */
    public function setId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setSku($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setType($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setName($value);

    /**
     * @param \Magento\Catalog\Model\Product $value
     * @return $this
     */
    public function setProduct($value);

    /**
     * @param float $value
     * @return $this
     */
    public function setQty($value);

    /**
     * @param float $value
     * @return $this
     */
    public function setPriceInclTax($value);

    /**
     * @param float $value
     * @return $this
     */
    public function setPriceExclTax($value);

    /**
     * @param mixed $value
     * @return $this
     */
    public function setItem($value);

    /**
     * @param \Qliro\QliroOne\Api\Product\TypeSourceItemInterface $value
     * @return $this
     */
    public function setParent($value);
}
