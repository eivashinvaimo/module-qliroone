<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Product pool class for faster access to quote item products
 */
class ProductPool
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Catalog\Model\Product[]
     */
    private $products = [];

    /**
     * Inject dependencies
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * Get a product by SKU
     *
     * @param string $sku
     * @param int|null $storeId
     * @return \Magento\Catalog\Model\Product
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProduct($sku, $storeId = null)
    {
        if (!isset($this->products[$sku])) {
            $this->products[$sku] = $this->productRepository->get($sku, false, $storeId);
        }

        return $this->products[$sku];
    }
}
