<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Builder;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Api\Data\CartInterface;
use Qliro\QliroOne\Api\Data\QliroOrderUpdateRequestInterfaceFactory;
use Qliro\QliroOne\Model\Config;
use \Qliro\QliroOne\Model\QliroOrder\Builder\OrderItemsBuilder;
/**
 * QliroOne Order update request builder class
 */
class UpdateRequestBuilder
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var \Qliro\QliroOne\Model\Config
     */
    private $qliroConfig;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Qliro\QliroOne\Api\Data\QliroOrderUpdateRequestInterfaceFactory
     */
    private $updateRequestFactory;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Builder\OrderItemsBuilder
     */
    private $orderItemsBuilder;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Builder\ShippingMethodsBuilder
     */
    private $shippingMethodsBuilder;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderUpdateRequestInterfaceFactory $updateRequestFactory
     * @param \Qliro\QliroOne\Model\Config $qliroConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Qliro\QliroOne\Model\QliroOrder\Builder\OrderItemsBuilder $orderItemsBuilder
     * @param \Qliro\QliroOne\Model\QliroOrder\Builder\ShippingMethodsBuilder $shippingMethodsBuilder
     */
    public function __construct(
        QliroOrderUpdateRequestInterfaceFactory $updateRequestFactory,
        Config $qliroConfig,
        ScopeConfigInterface $scopeConfig,
        OrderItemsBuilder $orderItemsBuilder,
        ShippingMethodsBuilder $shippingMethodsBuilder
    ) {
        $this->qliroConfig = $qliroConfig;
        $this->scopeConfig = $scopeConfig;
        $this->updateRequestFactory = $updateRequestFactory;
        $this->orderItemsBuilder = $orderItemsBuilder;
        $this->shippingMethodsBuilder = $shippingMethodsBuilder;
    }

    /**
     * Set quote for data extraction
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return $this
     */
    public function setQuote(CartInterface $quote)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Generate a QliroOne order update request object
     *
     * @return \Qliro\QliroOne\Api\Data\QliroOrderUpdateRequestInterface
     */
    public function create()
    {
        if (empty($this->quote)) {
            throw new \LogicException('Quote entity is not set.');
        }

        $updateRequest = $this->prepareUpdateRequest();

        $orderItems = $this->orderItemsBuilder->setQuote($this->quote)->create();

        $updateRequest->setOrderItems($orderItems);
        $shippingMethods = $this->shippingMethodsBuilder->setQuote($this->quote)->create();
        $updateRequest->setAvailableShippingMethods($shippingMethods->getAvailableShippingMethods());

        return $updateRequest;
    }

    /**
     * @return \Qliro\QliroOne\Api\Data\QliroOrderUpdateRequestInterface
     */
    private function prepareUpdateRequest()
    {
        /** @var \Qliro\QliroOne\Api\Data\QliroOrderUpdateRequestInterface $qliroOrderUpdateRequest */
        $qliroOrderUpdateRequest = $this->updateRequestFactory->create();
        $qliroOrderUpdateRequest->setRequireIdentityVerification($this->qliroConfig->requireIdentityVerification());

        return $qliroOrderUpdateRequest;
    }
}
