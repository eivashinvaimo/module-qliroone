<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\QliroOrder\Builder;

use Magento\Framework\Event\ManagerInterface;
use Magento\Quote\Model\Quote;
use Qliro\QliroOne\Api\Data\QliroOrderItemInterface;
use Qliro\QliroOne\Api\Data\QliroOrderItemInterfaceFactory;
use Qliro\QliroOne\Model\Config;

/**
 * QliroOne Order Item of type "Fee" builder class
 */
class FeeBuilder
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
     * @var \Qliro\QliroOne\Api\Data\QliroOrderItemInterfaceFactory
     */
    private $qliroOrderItemFactory;
    /**
     * @var \Qliro\QliroOne\Model\Fee
     */
    private $fee;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * Inject dependencies
     *
     * @param \Qliro\QliroOne\Model\Config $qliroConfig
     * @param \Qliro\QliroOne\Api\Data\QliroOrderItemInterfaceFactory $qliroOrderItemFactory
     * @param \Qliro\QliroOne\Model\Fee $fee
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        Config $qliroConfig,
        QliroOrderItemInterfaceFactory $qliroOrderItemFactory,
        \Qliro\QliroOne\Model\Fee $fee,
        ManagerInterface $eventManager
    ) {
        $this->qliroConfig = $qliroConfig;
        $this->qliroOrderItemFactory = $qliroOrderItemFactory;
        $this->fee = $fee;
        $this->eventManager = $eventManager;
    }

    /**
     * Set quote for data extraction
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function setQuote(Quote $quote)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Create a QliroOne order fee container
     *
     * Is this class used?
     *
     * @return \Qliro\QliroOne\Api\Data\QliroOrderItemInterface
     */
    public function create()
    {
        if (empty($this->quote)) {
            throw new \LogicException('Quote entity is not set.');
        }

        /** @var \Qliro\QliroOne\Api\Data\QliroOrderItemInterface $container */
        $container = $this->qliroOrderItemFactory->create();

        $priceExVat = $this->fee->getQlirooneFeeInclTax($this->quote);
        $priceIncVat = $this->fee->getQlirooneFeeExclTax($this->quote);

        $container->setMerchantReference($this->qliroConfig->getFeeMerchantReference());
        $container->setDescription($this->qliroConfig->getFeeMerchantReference());
        $container->setPricePerItemIncVat($priceIncVat);
        $container->setPricePerItemExVat($priceExVat);
        $container->setQuantity(1);
        $container->setType(QliroOrderItemInterface::TYPE_FEE);

        $this->eventManager->dispatch(
            'qliroone_order_item_build_after',
            [
                'quote' => $this->quote,
                'container' => $container,
            ]
        );

        $this->quote = null;

        return $container;
    }
}
