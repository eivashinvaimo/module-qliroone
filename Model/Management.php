<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Qliro\QliroOne\Api\Client\MerchantInterface;
use Qliro\QliroOne\Api\Client\OrderManagementInterface;
use Qliro\QliroOne\Api\Data\LinkInterface;
use Qliro\QliroOne\Api\Data\LinkInterfaceFactory;
use Qliro\QliroOne\Api\Data\QliroOrderCustomerInterface;
use Qliro\QliroOne\Api\Data\QliroOrderInterface;
use Qliro\QliroOne\Api\Data\UpdateShippingMethodsResponseInterface;
use Qliro\QliroOne\Api\Data\ValidateOrderNotificationInterface;
use Qliro\QliroOne\Api\Data\ValidateOrderResponseInterface;
use Qliro\QliroOne\Api\Data\CheckoutStatusInterface;
use Qliro\QliroOne\Api\Data\CheckoutStatusResponseInterface;
use Qliro\QliroOne\Api\Data\CheckoutStatusResponseInterfaceFactory;
use Qliro\QliroOne\Api\Data\QliroOrderManagementStatusInterface;
use Qliro\QliroOne\Api\Data\QliroOrderManagementStatusResponseInterface;
use Qliro\QliroOne\Api\Data\QliroOrderManagementStatusResponseInterfaceFactory;
use Qliro\QliroOne\Api\HashResolverInterface;
use Qliro\QliroOne\Api\LinkRepositoryInterface;
use Qliro\QliroOne\Api\ManagementInterface;
use Qliro\QliroOne\Model\Exception\LinkInactiveException;
use Qliro\QliroOne\Model\Logger\Manager as LogManager;
use Qliro\QliroOne\Model\Method\QliroOne;
use Qliro\QliroOne\Model\Order\OrderPlacer;
use Qliro\QliroOne\Model\OrderManagementStatus\Update\HandlerPool as  OrderManagementHandlerPool;
use Qliro\QliroOne\Model\QliroOrder\Admin\CancelOrderRequest;
use Qliro\QliroOne\Model\QliroOrder\Builder\CreateRequestBuilder;
use Qliro\QliroOne\Model\QliroOrder\Builder\UpdateRequestBuilder;
use Qliro\QliroOne\Api\Data\UpdateShippingMethodsNotificationInterface;
use Qliro\QliroOne\Model\QliroOrder\Builder\ShippingMethodsBuilder;
use Qliro\QliroOne\Model\QliroOrder\Builder\ValidateOrderBuilder;
use Qliro\QliroOne\Model\QliroOrder\Converter\CustomerConverter;
use Qliro\QliroOne\Model\QliroOrder\Converter\QuoteFromOrderConverter;
use Qliro\QliroOne\Model\QliroOrder\Converter\QuoteFromShippingMethodsConverter;
use Qliro\QliroOne\Model\QliroOrder\Converter\QuoteFromValidateConverter;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use Qliro\QliroOne\Model\ResourceModel\Lock;
use Magento\Framework\Serialize\Serializer\Json;
use Qliro\QliroOne\Model\Exception\TerminalException;
use Qliro\QliroOne\Model\Exception\FailToLockException;
use Qliro\QliroOne\Helper\Data as Helper;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Qliro\QliroOne\Api\Data\OrderManagementStatusInterfaceFactory;
use Qliro\QliroOne\Api\OrderManagementStatusRepositoryInterface;
use Qliro\QliroOne\Api\Data\OrderManagementStatusInterface;
use Qliro\QliroOne\Model\QliroOrder\Admin\Builder\InvoiceMarkItemsAsShippedRequestBuilder;
use Qliro\QliroOne\Model\QliroOrder\Admin\Builder\ShipmentMarkItemsAsShippedRequestBuilder;

/**
 * QliroOne management class
 */
class Management implements ManagementInterface
{
    const REFERENCE_MIN_LENGTH = 6;

    const QLIRO_SKIP_ACTUAL_CAPTURE = 'qliro_skip_actual_capture';

    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Qliro\QliroOne\Model\Config
     */
    private $qliroConfig;

    /**
     * @var \Qliro\QliroOne\Api\Client\MerchantInterface
     */
    private $merchantApi;

    /**
     * @var \Qliro\QliroOne\Api\Client\OrderManagementInterface
     */
    private $orderManagementApi;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Builder\CreateRequestBuilder
     */
    private $createRequestBuilder;

    /**
     * @var \Qliro\QliroOne\Api\Data\LinkInterfaceFactory
     */
    private $linkFactory;

    /**
     * @var \Qliro\QliroOne\Api\LinkRepositoryInterface
     */
    private $linkRepository;

    /**
     * @var \Qliro\QliroOne\Api\HashResolverInterface
     */
    private $hashResolver;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Builder\ShippingMethodsBuilder
     */
    private $shippingMethodsBuilder;

    /**
     * @var \Qliro\QliroOne\Model\ContainerMapper
     */
    private $containerMapper;

    /**
     * @var \Qliro\QliroOne\Model\Logger\Manager
     */
    private $logManager;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Builder\ValidateOrderBuilder
     */
    private $validateOrderBuilder;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Converter\QuoteFromValidateConverter
     */
    private $quoteFromValidateConverter;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Converter\QuoteFromShippingMethodsConverter
     */
    private $quoteFromShippingMethodsConverter;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Converter\QuoteFromOrderConverter
     */
    private $quoteFromOrderConverter;

    /**
     * @var \Qliro\QliroOne\Model\Order\OrderPlacer
     */
    private $orderPlacer;

    /**
     * @var \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
     */
    private $transactionBuilder;

    /**
     * @var \Qliro\QliroOne\Model\ResourceModel\Lock
     */
    private $lock;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Builder\UpdateRequestBuilder
     */
    private $updateRequestBuilder;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $json;

    /**
     * @var \Qliro\QliroOne\Model\QliroOrder\Converter\CustomerConverter
     */
    private $customerConverter;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Qliro\QliroOne\Api\Data\CheckoutStatusResponseInterfaceFactory
     */
    private $checkoutStatusResponseFactory;

    /**
     * @var \Qliro\QliroOne\Model\Fee
     */
    private $fee;

    /**
     * @var \Qliro\QliroOne\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
     */
    private $orderSender;

    /**
     * @var QliroOrderManagementStatusResponseInterfaceFactory
     */
    private $qliroOrderManagementStatusResponseFactory;

    /**
     * @var \Qliro\QliroOne\Api\Data\OrderManagementStatusInterfaceFactory
     */
    private $orderManagementStatusInterfaceFactory;

    /**
     * @var OrderManagementStatusRepositoryInterface
     */
    private $orderManagementStatusRepository;

    /**
     * @var QliroOrder\Admin\Builder\InvoiceMarkItemsAsShippedRequestBuilder
     */
    private $invoiceMarkItemsAsShippedRequestBuilder;

    /**
     * @var QliroOrder\Admin\Builder\ShipmentMarkItemsAsShippedRequestBuilder
     */
    private $shipmentMarkItemsAsShippedRequestBuilder;

    /**
     * @var OrderManagementStatus\Update\HandlerPool
     */
    private $statusUpdateHandlerPool;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * Inject dependencies
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Qliro\QliroOne\Model\Config $qliroConfig
     * @param \Qliro\QliroOne\Api\Client\MerchantInterface $merchantApi
     * @param \Qliro\QliroOne\Api\Client\OrderManagementInterface $orderManagementApi
     * @param \Qliro\QliroOne\Model\QliroOrder\Builder\CreateRequestBuilder $createRequestBuilder
     * @param \Qliro\QliroOne\Model\QliroOrder\Builder\UpdateRequestBuilder $updateRequestBuilder
     * @param \Qliro\QliroOne\Model\QliroOrder\Builder\ShippingMethodsBuilder $shippingMethodsBuilder
     * @param \Qliro\QliroOne\Model\QliroOrder\Builder\ValidateOrderBuilder $validateOrderBuilder
     * @param \Qliro\QliroOne\Api\Data\CheckoutStatusResponseInterfaceFactory $checkoutStatusResponseFactory
     * @param \Qliro\QliroOne\Model\QliroOrder\Converter\QuoteFromValidateConverter $quoteFromValidateConverter
     * @param \Qliro\QliroOne\Model\QliroOrder\Converter\QuoteFromShippingMethodsConverter $quoteFromShippingConverter
     * @param \Qliro\QliroOne\Model\QliroOrder\Converter\CustomerConverter $customerConverter
     * @param \Qliro\QliroOne\Model\QliroOrder\Converter\QuoteFromOrderConverter $quoteFromOrderConverter
     * @param \Qliro\QliroOne\Api\Data\LinkInterfaceFactory $linkFactory
     * @param \Qliro\QliroOne\Api\LinkRepositoryInterface $linkRepository
     * @param \Qliro\QliroOne\Api\HashResolverInterface $hashResolver
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Qliro\QliroOne\Model\ContainerMapper $containerMapper
     * @param \Qliro\QliroOne\Model\Logger\Manager $logManager
     * @param \Qliro\QliroOne\Model\Order\OrderPlacer $orderPlacer
     * @param \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder
     * @param \Qliro\QliroOne\Model\ResourceModel\Lock $lock
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Qliro\QliroOne\Model\Fee $fee
     * @param \Qliro\QliroOne\Helper\Data $helper
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
     * @param QliroOrderManagementStatusResponseInterfaceFactory $qliroOrderManagementStatusResponseFactory
     * @param \Qliro\QliroOne\Api\Data\OrderManagementStatusInterfaceFactory $orderManagementStatusInterfaceFactory
     * @param \Qliro\QliroOne\Api\OrderManagementStatusRepositoryInterface $orderManagementStatusRepository
     * @param InvoiceMarkItemsAsShippedRequestBuilder $invoiceMarkItemsAsShippedRequestBuilder
     * @param ShipmentMarkItemsAsShippedRequestBuilder $shipmentMarkItemsAsShippedRequestBuilder
     * @param \Qliro\QliroOne\Model\OrderManagementStatus\Update\HandlerPool $statusUpdateHandlerPool
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Config $qliroConfig,
        MerchantInterface $merchantApi,
        OrderManagementInterface $orderManagementApi,
        CreateRequestBuilder $createRequestBuilder,
        UpdateRequestBuilder $updateRequestBuilder,
        ShippingMethodsBuilder $shippingMethodsBuilder,
        ValidateOrderBuilder $validateOrderBuilder,
        CheckoutStatusResponseInterfaceFactory $checkoutStatusResponseFactory,
        QuoteFromValidateConverter $quoteFromValidateConverter,
        QuoteFromShippingMethodsConverter $quoteFromShippingConverter,
        CustomerConverter $customerConverter,
        QuoteFromOrderConverter $quoteFromOrderConverter,
        LinkInterfaceFactory $linkFactory,
        LinkRepositoryInterface $linkRepository,
        HashResolverInterface $hashResolver,
        CartRepositoryInterface $quoteRepository,
        OrderRepositoryInterface $orderRepository,
        ContainerMapper $containerMapper,
        LogManager $logManager,
        OrderPlacer $orderPlacer,
        BuilderInterface $transactionBuilder,
        Lock $lock,
        Json $json,
        Fee $fee,
        Helper $helper,
        OrderSender $orderSender,
        QliroOrderManagementStatusResponseInterfaceFactory $qliroOrderManagementStatusResponseFactory,
        OrderManagementStatusInterfaceFactory $orderManagementStatusInterfaceFactory,
        OrderManagementStatusRepositoryInterface $orderManagementStatusRepository,
        InvoiceMarkItemsAsShippedRequestBuilder $invoiceMarkItemsAsShippedRequestBuilder,
        ShipmentMarkItemsAsShippedRequestBuilder $shipmentMarkItemsAsShippedRequestBuilder,
        OrderManagementHandlerPool $statusUpdateHandlerPool,
        ManagerInterface $eventManager
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->qliroConfig = $qliroConfig;
        $this->merchantApi = $merchantApi;
        $this->orderManagementApi = $orderManagementApi;
        $this->createRequestBuilder = $createRequestBuilder;
        $this->linkFactory = $linkFactory;
        $this->linkRepository = $linkRepository;
        $this->hashResolver = $hashResolver;
        $this->quoteRepository = $quoteRepository;
        $this->shippingMethodsBuilder = $shippingMethodsBuilder;
        $this->containerMapper = $containerMapper;
        $this->logManager = $logManager;
        $this->validateOrderBuilder = $validateOrderBuilder;
        $this->quoteFromValidateConverter = $quoteFromValidateConverter;
        $this->quoteFromShippingMethodsConverter = $quoteFromShippingConverter;
        $this->quoteFromOrderConverter = $quoteFromOrderConverter;
        $this->orderPlacer = $orderPlacer;
        $this->transactionBuilder = $transactionBuilder;
        $this->lock = $lock;
        $this->updateRequestBuilder = $updateRequestBuilder;
        $this->json = $json;
        $this->customerConverter = $customerConverter;
        $this->orderRepository = $orderRepository;
        $this->checkoutStatusResponseFactory = $checkoutStatusResponseFactory;
        $this->fee = $fee;
        $this->helper = $helper;
        $this->orderSender = $orderSender;
        $this->qliroOrderManagementStatusResponseFactory = $qliroOrderManagementStatusResponseFactory;
        $this->orderManagementStatusInterfaceFactory = $orderManagementStatusInterfaceFactory;
        $this->orderManagementStatusRepository = $orderManagementStatusRepository;
        $this->invoiceMarkItemsAsShippedRequestBuilder = $invoiceMarkItemsAsShippedRequestBuilder;
        $this->shipmentMarkItemsAsShippedRequestBuilder = $shipmentMarkItemsAsShippedRequestBuilder;
        $this->statusUpdateHandlerPool = $statusUpdateHandlerPool;
        $this->eventManager = $eventManager;
    }

    /**
     * Set quote to the Management class
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Fetch a QliroOne order and return it as a container
     *
     * @param bool $allowRecreate
     * @return \Qliro\QliroOne\Api\Data\QliroOrderInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     */
    public function getQliroOrder($allowRecreate = true)
    {
        $link = $this->getLinkFromQuote();
        $this->logManager->setMark('GET QLIRO ORDER');

        $qliroOrder = null; // Logical placeholder, may never happen

        try {
            $qliroOrderId = $link->getQliroOrderId();
            $qliroOrder = $this->merchantApi->getOrder($qliroOrderId);

            if ($this->lock->lock($qliroOrderId)) {
                if (empty($link->getOrderId())) {
                    if ($qliroOrder->isPlaced()) {
                        if ($allowRecreate) {
                            $link->setIsActive(false);
                            $this->linkRepository->save($link);

                            return $this->getQliroOrder(false);
                        }
                        /*
                        * Reaching this point implies that the link between Qliro and Magento is out of sync.
                        * It should not happen.
                        */
                        throw new \LogicException('Order has already been processed.');
                    }
                    try {
                        $this->quoteFromOrderConverter->convert($qliroOrder, $this->getQuote());
                        $this->recalculateAndSaveQuote();
                    } catch (\Exception $exception) {
                        $this->logManager->debug(
                            $exception,
                            [
                                'extra' => [
                                    'link_id' => $link->getId(),
                                    'quote_id' => $link->getQuoteId(),
                                    'qliro_order_id' => $qliroOrderId,
                                ],
                            ]
                        );

                        $this->lock->unlock($qliroOrderId);
                        throw $exception;
                    }
                }

                $this->lock->unlock($qliroOrderId);
            } else {
                $this->logManager->debug(
                    'An order is in preparation, not possible to update the quote',
                    [
                        'extra' => [
                            'link_id' => $link->getId(),
                            'quote_id' => $link->getQuoteId(),
                            'qliro_order_id' => $qliroOrderId,
                        ],
                    ]
                );
            }
        } catch (\Exception $exception) {
            $this->logManager->debug(
                $exception,
                [
                    'extra' => [
                        'link_id' => $link->getId(),
                        'quote_id' => $link->getQuoteId(),
                        'qliro_order_id' => $qliroOrderId ?? null,
                    ],
                ]
            );

            throw new TerminalException('Couldn\'t fetch the QliroOne order.', null, $exception);
        } finally {
            $this->logManager->setMark(null);
        }

        return $qliroOrder;
    }

    /**
     * Fetch an HTML snippet from QliroOne order
     *
     * @return string
     */
    public function getHtmlSnippet()
    {
        try {
            return $this->doFetchHtmlSnippet();
        } catch (\Exception $exception) {
            $openTag = '<a href="javascript:;" onclick="location.reload(true)">';
            $closeTag = '</a>';

            return __('QliroOne Checkout has failed to load. Please try to %1reload page%2.', $openTag, $closeTag);
        }
    }

    /**
     * Update quote with received data in the container and return a list of available shipping methods
     *
     * @param \Qliro\QliroOne\Api\Data\UpdateShippingMethodsNotificationInterface $updateContainer
     * @return \Qliro\QliroOne\Api\Data\UpdateShippingMethodsResponseInterface
     */
    public function getShippingMethods(UpdateShippingMethodsNotificationInterface $updateContainer)
    {
        /** @var \Qliro\QliroOne\Api\Data\UpdateShippingMethodsResponseInterface $declineContainer */
        $declineContainer = $this->containerMapper->fromArray(
            ['DeclineReason' => UpdateShippingMethodsResponseInterface::REASON_POSTAL_CODE],
            UpdateShippingMethodsResponseInterface::class
        );

        try {
            $link = $this->linkRepository->getByQliroOrderId($updateContainer->getOrderId());
            $this->logManager->setMerchantReference($link->getReference());

            try {
                $this->quote = $this->quoteRepository->get($link->getQuoteId());

                $this->quoteFromShippingMethodsConverter->convert($updateContainer, $this->getQuote());
                $this->recalculateAndSaveQuote();

                return $this->shippingMethodsBuilder->setQuote($this->getQuote())->create();
            } catch (\Exception $exception) {
                $this->logManager->critical(
                    $exception,
                    [
                        'extra' => [
                            'qliro_order_id' => $updateContainer->getOrderId(),
                            'quote_id' => $link->getQuoteId(),
                        ],
                    ]
                );

                return $declineContainer;
            }
        } catch (\Exception $exception) {
            $this->logManager->critical(
                $exception,
                [
                    'extra' => [
                        'qliro_order_id' => $updateContainer->getOrderId(),
                    ],
                ]
            );

            return $declineContainer;
        }
    }

    /**
     * Update quote with received data in the container and validate QliroOne order
     *
     * @param \Qliro\QliroOne\Api\Data\ValidateOrderNotificationInterface $validateContainer
     * @return \Qliro\QliroOne\Api\Data\ValidateOrderResponseInterface
     */
    public function validateQliroOrder(ValidateOrderNotificationInterface $validateContainer)
    {
        /** @var \Qliro\QliroOne\Api\Data\ValidateOrderResponseInterface $responseContainer */
        $responseContainer = $this->containerMapper->fromArray(
            ['DeclineReason' => ValidateOrderResponseInterface::REASON_OTHER],
            ValidateOrderResponseInterface::class
        );

        try {
            $link = $this->linkRepository->getByQliroOrderId($validateContainer->getOrderId());
            $this->logManager->setMerchantReference($link->getReference());

            try {
                $this->quote = $this->quoteRepository->get($link->getQuoteId());
                $this->quoteFromValidateConverter->convert($validateContainer, $this->getQuote());
                $this->recalculateAndSaveQuote();

                return $this->validateOrderBuilder->setQuote($this->getQuote())->setValidationRequest(
                        $validateContainer
                    )->create();
            } catch (\Exception $exception) {
                $this->logManager->critical(
                    $exception,
                    [
                        'extra' => [
                            'qliro_order_id' => $validateContainer->getOrderId(),
                            'quote_id' => $link->getQuoteId(),
                        ],
                    ]
                );

                return $responseContainer;
            }
        } catch (\Exception $exception) {
            $this->logManager->critical(
                $exception,
                [
                    'extra' => [
                        'qliro_order_id' => $validateContainer->getOrderId(),
                    ],
                ]
            );

            return $responseContainer;
        }
    }

    /**
     * Poll for Magento order placement and return order increment ID if successful
     *
     * @return string
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     */
    public function pollPlaceOrder()
    {
        $quoteId = $this->quote->getId();

        try {
            $link = $this->linkRepository->getByQuoteId($quoteId);
            $this->logManager->setMerchantReference($link->getReference());
            $orderId = $link->getOrderId();
            $qliroOrderId = $link->getQliroOrderId();

            if (empty($orderId)) {
                try {
                    $responseContainer = $this->merchantApi->getOrder($qliroOrderId);
                    $order = $this->placeOrder($responseContainer);
                } catch (FailToLockException $exception) {
                    $this->logManager->critical(
                        $exception,
                        [
                            'extra' => [
                                'quote_id' => $quoteId,
                                'qliro_order_id' => $qliroOrderId,
                            ],
                        ]
                    );

                    throw $exception;
                } catch (\Exception $exception) {
                    $this->logManager->critical(
                        $exception,
                        [
                            'extra' => [
                                'quote_id' => $quoteId,
                                'qliro_order_id' => $qliroOrderId,
                            ],
                        ]
                    );

                    throw new TerminalException('Order placement failed', null, $exception);
                }
            } else {
                $order = $this->orderRepository->get($orderId);
            }
        } catch (NoSuchEntityException $exception) {
            $this->logManager->critical(
                $exception,
                [
                    'extra' => [
                        'quote_id' => $quoteId,
                        'order_id' => $orderId ?? null,
                        'qliro_order_id' => $qliroOrderId ?? null,
                    ],
                ]
            );
            throw new TerminalException('Failed to link current session with Qliro One order', null, $exception);
        } catch (\Exception $exception) {
            $this->logManager->critical(
                $exception,
                [
                    'extra' => [
                        'quote_id' => $quoteId,
                        'order_id' => $orderId ?? null,
                        'qliro_order_id' => $qliroOrderId ?? null,
                    ],
                ]
            );

            throw new TerminalException('Something went wrong during order placement polling', null, $exception);
        }

        return $order->getIncrementId();
    }

    /**
     * @param \Qliro\QliroOne\Api\Data\CheckoutStatusInterface $checkoutStatus
     * @return \Qliro\QliroOne\Api\Data\CheckoutStatusResponseInterface
     */
    public function checkoutStatus(CheckoutStatusInterface $checkoutStatus)
    {
        $qliroOrderId = $checkoutStatus->getOrderId();

        try {
            try {
                $link = $this->linkRepository->getByQliroOrderId($qliroOrderId);
            } catch (NoSuchEntityException $exception) {
                $this->handleOrderCancelationIfRequired($checkoutStatus);
                throw $exception;
            }

            $this->logManager->setMerchantReference($link->getReference());

            $link->setQliroOrderStatus($checkoutStatus->getStatus());
            $this->linkRepository->save($link);

            $orderId = $link->getOrderId();

            if (empty($orderId)) {
                /*
                 * First major scenario:
                 * There is not yet any Magento order. Attempt to create the order, placeOrder()
                 * will process the created order based on the QliroOne order status as found in the link.
                 */

                try {
                    // TODO: the quote is still active, so the shopper might be adding more items
                    // TODO: without knowing that there is no order yet
                    $responseContainer = $this->merchantApi->getOrder($qliroOrderId);
                    $this->placeOrder($responseContainer);

                    return $this->checkoutStatusRespond(CheckoutStatusResponseInterface::RESPONSE_RECEIVED);
                } catch (FailToLockException $exception) {
                    /*
                     * Someone else is creating the order at the moment. Let Qliro try again in a few minutes.
                     */
                    $this->logManager->critical(
                        $exception,
                        [
                            'extra' => [
                                'qliro_order_id' => $qliroOrderId,
                            ],
                        ]
                    );

                    return $this->checkoutStatusRespond(CheckoutStatusResponseInterface::RESPONSE_ORDER_NOT_FOUND);
                } catch (\Exception $exception) {
                    $this->logManager->critical(
                        $exception,
                        [
                            'extra' => [
                                'qliro_order_id' => $qliroOrderId,
                            ],
                        ]
                    );

                    return $this->checkoutStatusRespond(CheckoutStatusResponseInterface::RESPONSE_ORDER_NOT_FOUND);
                }
            } else {
                /*
                 * Second major scenario:
                 * The order already exists; just update the order with the new QliroOne order status
                 */
                if ($this->applyQliroOrderStatus($this->orderRepository->get($orderId))) {
                    return $this->checkoutStatusRespond(CheckoutStatusResponseInterface::RESPONSE_RECEIVED);
                } else {
                    return $this->checkoutStatusRespond(CheckoutStatusResponseInterface::RESPONSE_ORDER_NOT_FOUND);
                }
            }
        } catch (NoSuchEntityException $exception) {
            /* no more qliro pushes should be sent */
            return $this->checkoutStatusRespond(CheckoutStatusResponseInterface::RESPONSE_RECEIVED);
        } catch (\Exception $exception) {
            $this->logManager->critical(
                $exception,
                [
                    'extra' => [
                        'qliro_order_id' => $qliroOrderId,
                    ],
                ]
            );

            return $this->checkoutStatusRespond(CheckoutStatusResponseInterface::RESPONSE_ORDER_NOT_FOUND);
        }
    }

    /**
     * Get a QliroOne order, update the quote, then place Magento order
     * If placeOrder is successful, it returns the Magento Order
     * If an error occurs it returns null
     * If it's not possible to aquire lock, it returns false
     *
     * @param \Qliro\QliroOne\Api\Data\QliroOrderInterface $qliroOrder
     * @param string $state
     * @return \Magento\Sales\Model\Order
     * @throws \Qliro\QliroOne\Model\Exception\FailToLockException
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     * @todo May require doing something upon $this->applyQliroOrderStatus($orderId) returning false
     */
    public function placeOrder(QliroOrderInterface $qliroOrder, $state = Order::STATE_PENDING_PAYMENT)
    {
        $qliroOrderId = $qliroOrder->getOrderId();

        if (!$this->lock->lock($qliroOrderId)) {
            throw new FailToLockException(__('Failed to aquire lock when placing order'));
        }

        $this->logManager->setMark('PLACE ORDER');
        $order = null; // Placeholder, this method may never return null as an order

        try {
            $link = $this->linkRepository->getByQliroOrderId($qliroOrderId);

            try {
                if ($orderId = $link->getOrderId()) {
                    $this->logManager->debug(
                        'Order is already created, skipping',
                        [
                            'extra' => [
                                'qliro_order' => $qliroOrderId,
                                'quote_id' => $this->quote->getId(),
                                'order_id' => $orderId,
                            ],
                        ]
                    );

                    $order = $this->orderRepository->get($orderId);
                } else {
                    $this->quote = $this->quoteRepository->get($link->getQuoteId());

                    $this->logManager->debug(
                        'Placing order',
                        [
                            'extra' => [
                                'qliro_order' => $qliroOrderId,
                                'quote_id' => $this->quote->getId(),
                            ],
                        ]
                    );

                    $this->quoteFromOrderConverter->convert($qliroOrder, $this->getQuote());
                    $this->addAdditionalInfoToQuote($link, $qliroOrder->getPaymentMethod());
                    $this->recalculateAndSaveQuote();

                    $order = $this->orderPlacer->place($this->getQuote());
                    $orderId = $order->getId();

                    $link->setOrderId($orderId);
                    $this->linkRepository->save($link);

                    $this->createPaymentTransaction($order, $qliroOrder, $state);

                    $this->logManager->debug(
                        'Order placed successfully',
                        [
                            'extra' => [
                                'qliro_order' => $qliroOrderId,
                                'quote_id' => $this->quote->getId(),
                                'order_id' => $orderId,
                            ],
                        ]
                    );
                }

                $this->applyQliroOrderStatus($order);

                $link->setMessage(sprintf('Created order %s', $order->getIncrementId()));
                $this->linkRepository->save($link);
            } catch (\Exception $exception) {
                $link->setIsActive(false);
                $link->setMessage($exception->getMessage());
                $this->linkRepository->save($link);

                $this->logManager->critical(
                    $exception,
                    [
                        'extra' => [
                            'qliro_order_id' => $qliroOrderId,
                            'quote_id' => $link->getQuoteId(),
                        ],
                    ]
                );

                throw $exception;
            }
        } catch (\Exception $exception) {
            $this->logManager->critical(
                $exception,
                [
                    'extra' => [
                        'qliro_order_id' => $qliroOrderId,
                    ],
                ]
            );

            throw new TerminalException($exception->getMessage(), $exception->getCode(), $exception);
        } finally {
            $this->lock->unlock($qliroOrderId);
            $this->logManager->setMark(null);
        }

        return $order;
    }

    /**
     * Create payment transaction, which will hold and handle the Order Management features.
     * This saves payment and transaction, possibly also the order.
     *
     * This should have been done differently, with authorization keyword in method etc...
     *
     * @param \Magento\Sales\Model\Order $order
     * @param QliroOrderInterface $qliroOrder
     * @param string $state
     * @throws \Exception
     */
    public function createPaymentTransaction($order, $qliroOrder, $state = Order::STATE_PENDING_PAYMENT)
    {
        $this->logManager->setMark('PAYMENT TRANSACTION');

        try {
            /** @var \Magento\Sales\Model\Order\Payment $payment */
            $payment = $order->getPayment();

            $payment->setLastTransId($qliroOrder->getOrderId());
            $transactionId = 'qliroone-' . $qliroOrder->getOrderId();
            $payment->setTransactionId($transactionId);
            $payment->setIsTransactionClosed(false);

            $formattedPrice = $order->getBaseCurrency()->formatTxt(
                $order->getGrandTotal()
            );

            $message = __('Qliro One authorized amount of %1.', $formattedPrice);

            /** @var \Magento\Sales\Api\Data\TransactionInterface $transaction */
            $transaction = $this->transactionBuilder->setPayment($payment)->setOrder($order)->setTransactionId(
                    $payment->getTransactionId()
                )->build(\Magento\Sales\Api\Data\TransactionInterface::TYPE_AUTH);

            $payment->addTransactionCommentsToOrder($transaction, $message);
            $payment->setSkipOrderProcessing(true);
            $payment->save();

            if (empty($status)) {
                if ($order->getState() != $state) {
                    $order->setState($state);
                    $this->orderRepository->save($order);
                }
            } else {
                if ($order->getState() != $state || $order->getStatus() != $status) {
                    $order->setState($state)->setStatus($status);
                    $this->orderRepository->save($order);
                }
            }

            $transaction->save();
        } catch (\Exception $exception) {
            throw $exception;
        } finally {
            $this->logManager->setMark(null);
        }
    }

    /**
     * Update qliro order with information in quote
     *
     * @param int $orderId
     * @param bool $force
     */
    public function updateQliroOrder($orderId, $force = false)
    {
        $this->logManager->setMark('UPDATE ORDER');

        try {
            $link = $this->linkRepository->getByQliroOrderId($orderId);
            $this->logManager->setMerchantReference($link->getReference());

            $isQliroOrderStatusEmpty = empty($link->getQliroOrderStatus());
            $isQliroOrderStatusInProcess = $link->getQliroOrderStatus() == CheckoutStatusInterface::STATUS_IN_PROCESS;

            if ($isQliroOrderStatusEmpty || $isQliroOrderStatusInProcess) {
                $this->logManager->debug('update qliro order');
                $quoteId = $link->getQuoteId();

                try {
                    /** @var \Magento\Quote\Model\Quote $quote */
                    $quote = $this->quoteRepository->get($quoteId);

                    $hash = $this->generateUpdateHash($quote);

                    $this->logManager->debug(
                        sprintf(
                            'order hash is %s',
                            $link->getQuoteSnapshot() === $hash ? 'same' : 'different'
                        )
                    );

                    if ($force || $this->canUpdateOrder($hash, $link)) {
                        $request = $this->updateRequestBuilder->setQuote($quote)->create();
                        $this->merchantApi->updateOrder($orderId, $request);
                        $link->setQuoteSnapshot($hash);
                        $this->linkRepository->save($link);
                        $this->logManager->debug(sprintf('updated order %s', $orderId));
                    }
                } catch (\Exception $exception) {
                    $this->logManager->critical(
                        $exception,
                        [
                            'extra' => [
                                'qliro_order_id' => $orderId,
                                'quote_id' => $quoteId,
                                'link_id' => $link->getId()
                            ],
                        ]
                    );
                }
            } else {
                $this->logManager->debug('Can\'t update QliroOne order');
            }
        } catch (\Exception $exception) {
            $this->logManager->critical(
                $exception,
                [
                    'extra' => [
                        'qliro_order_id' => $orderId,
                    ],
                ]
            );
        } finally {
            $this->logManager->setMark(null);
        }
    }

    /**
     * Update customer with data from QliroOne frontend callback
     *
     * @param array $customerData
     * @throws \Exception
     */
    public function updateCustomer($customerData)
    {
        /** @var \Qliro\QliroOne\Api\Data\QliroOrderCustomerInterface $qliroCustomer */
        $qliroCustomer = $this->containerMapper->fromArray($customerData, QliroOrderCustomerInterface::class);

        $this->customerConverter->convert($qliroCustomer, $this->getQuote());
        $this->recalculateAndSaveQuote();
    }

    /**
     * Update selected shipping method in quote
     * Return true in case shipping method was set, or false if the quote is virtual or method was not changed
     *
     * @param string $code
     * @param string|null $secondaryOption
     * @param float|null $price
     * @return bool
     * @throws \Exception
     */
    public function updateShippingMethod($code, $secondaryOption = null, $price = null)
    {
        $quote = $this->getQuote();

        if ($code && !$quote->isVirtual()) {
            $shippingAddress = $quote->getShippingAddress();

            if (!$shippingAddress->getPostcode()) {
                $billingAddress = $quote->getBillingAddress();
                $shippingAddress->addData(
                    [
                        'email' => $billingAddress->getEmail(),
                        'firstname' => $billingAddress->getFirstname(),
                        'lastname' => $billingAddress->getLastname(),
                        'company' => $billingAddress->getCompany(),
                        'street' => $billingAddress->getStreetFull(),
                        'city' => $billingAddress->getCity(),
                        'region' => $billingAddress->getRegion(),
                        'region_id' => $billingAddress->getRegionId(),
                        'postcode' => $billingAddress->getPostcode(),
                        'country_id' => $billingAddress->getCountryId(),
                        'telephone' => $billingAddress->getTelephone(),
                        'same_as_billing' => true,
                    ]
                );
            }

            // @codingStandardsIgnoreStart
            // phpcs:disable
            $container = new DataObject(
                [
                    'shipping_method' => $code,
                    'secondary_option' => $secondaryOption,
                    'shipping_price' => $price,
                    'can_save_quote' => $shippingAddress->getShippingMethod() !== $code,
                ]
            );
            // @codingStandardsIgnoreEnd
            // phpcs:enable

            $this->eventManager->dispatch(
                'qliroone_shipping_method_update_before',
                [
                    'quote' => $quote,
                    'container' => $container,
                ]
            );

            if (!$container->getCanSaveQuote()) {
                return false;
            }

            $shippingAddress->setShippingMethod($container->getShippingMethod());
            $this->recalculateAndSaveQuote();

            // For some reason shipping code that was previously set, is not applied
            if ($shippingAddress->getShippingMethod() !== $container->getShippingMethod()) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Update shipping price in quote
     * Return true in case shipping price was set, or false if the quote is virtual or update didn't happen
     *
     * @param float|null $price
     * @return bool
     * @throws \Exception
     */
    public function updateShippingPrice($price)
    {
        $quote = $this->getQuote();

        if ($price && !$quote->isVirtual()) {
            // @codingStandardsIgnoreStart
            // phpcs:disable
            $container = new DataObject(
                [
                    'shipping_price' => $price,
                    'can_save_quote' => false,
                ]
            );
            // @codingStandardsIgnoreEnd
            // phpcs:enable

            $this->eventManager->dispatch(
                'qliroone_shipping_price_update_before',
                [
                    'quote' => $quote,
                    'container' => $container,
                ]
            );

            if ($container->getCanSaveQuote()) {
                $this->recalculateAndSaveQuote();

                return true;
            }
        }

        return false;
    }

    /**
     * Update selected shipping method in quote
     * Return true in case shipping method was set, or false if the quote is virtual or method was not changed
     *
     * @param float $fee
     * @return bool
     * @throws \Exception
     */
    public function updateFee($fee)
    {
        try {
            $this->fee->setQlirooneFeeInclTax($this->getQuote(), $fee);
            $this->recalculateAndSaveQuote();
        } catch (\Exception $exception) {
            $link = $this->getLinkFromQuote();
            $this->logManager->critical(
                $exception,
                [
                    'extra' => [
                        'qliro_order_id' => $link->getOrderId(),
                    ],
                ]
            );

            return false;
        }

        return true;
    }

    /**
     * Cancel QliroOne order
     *
     * @param int $qliroOrderId
     * @return \Qliro\QliroOne\Api\Data\AdminTransactionResponseInterface
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     */
    public function cancelQliroOrder($qliroOrderId)
    {
        $this->logManager->setMark('CANCEL QLIRO ORDER');

        $responseContainer = null; // Logical placeholder, returning null may never happen

        try {
            /** @var \Qliro\QliroOne\Model\QliroOrder\Admin\CancelOrderRequest $request */
            $request = $this->containerMapper->fromArray(
                ['OrderId' => $qliroOrderId],
                CancelOrderRequest::class
            );

            /*
             * First we try to load an active link, then, when it fails, we try to load the inactive link
             * and throw a specific exception if that exists.
             */
            try {
                $link = $this->linkRepository->getByQliroOrderId($qliroOrderId);
            } catch (NoSuchEntityException $exception) {
                $this->linkRepository->getByQliroOrderId($qliroOrderId, false);
                throw new LinkInactiveException('This order has already been processed and the link deactivated.');
            }

            $responseContainer = $this->orderManagementApi->cancelOrder($request);

            /** @var \Qliro\QliroOne\Model\OrderManagementStatus $omStatus */
            $omStatus = $this->orderManagementStatusInterfaceFactory->create();

            $omStatus->setRecordType(OrderManagementStatusInterface::RECORD_TYPE_CANCEL);
            $omStatus->setRecordId($link->getOrderId());
            $omStatus->setTransactionId($responseContainer->getPaymentTransactionId());
            $omStatus->setTransactionStatus($responseContainer->getStatus());
            $omStatus->setNotificationStatus(OrderManagementStatus::NOTIFICATION_STATUS_DONE);
            $omStatus->setMessage('Cancellation requested');
            $omStatus->setQliroOrderId($qliroOrderId);
            $this->orderManagementStatusRepository->save($omStatus);
        } catch (LinkInactiveException $exception) {
            throw new TerminalException(
                'Couldn\'t request to cancel QliroOne order with inactive link.',
                null,
                $exception
            );
        } catch (\Exception $exception) {
            $logData = [
                'qliro_order_id' => $qliroOrderId,
            ];

            if (isset($omStatus)) {
                $logData = array_merge($logData, [
                    'transaction_id' => $omStatus->getTransactionId(),
                    'transaction_status' => $omStatus->getTransactionStatus(),
                    'record_type' => $omStatus->getRecordType(),
                    'record_id' => $omStatus->getRecordId(),
                ]);
            }

            $this->logManager->critical(
                $exception,
                [
                    'extra' => $logData,
                ]
            );

            throw new TerminalException('Couldn\'t request to cancel QliroOne order.', null, $exception);
        } finally {
            $this->logManager->setMark(null);
        }

        return $responseContainer;
    }

    /**
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return void
     * @throws \Exception
     */
    public function captureByInvoice($payment, $amount)
    {
        if ($payment->getData(self::QLIRO_SKIP_ACTUAL_CAPTURE)) {
            return;
        }
        $this->invoiceMarkItemsAsShippedRequestBuilder->setPayment($payment);
        $this->invoiceMarkItemsAsShippedRequestBuilder->setAmount($amount);

        $request = $this->invoiceMarkItemsAsShippedRequestBuilder->create();
        $result = $this->orderManagementApi->markItemsAsShipped($request);

        try {
            /** @var \Qliro\QliroOne\Model\OrderManagementStatus $omStatus */
            $omStatus = $this->orderManagementStatusInterfaceFactory->create();
            $omStatus->setRecordId($payment->getId());
            $omStatus->setRecordType(OrderManagementStatusInterface::RECORD_TYPE_PAYMENT);
            $omStatus->setTransactionId($result->getPaymentTransactionId());
            $omStatus->setTransactionStatus(QliroOrderManagementStatusInterface::STATUS_CREATED);
            $omStatus->setNotificationStatus(OrderManagementStatus::NOTIFICATION_STATUS_DONE);
            $omStatus->setMessage('Capture Requested for Invoice');

            /** @var \Magento\Sales\Model\Order $order */
            $order = $payment->getOrder();
            $link = $this->linkRepository->getByOrderId($order->getId());
            $omStatus->setQliroOrderId($link->getQliroOrderId());

            $this->orderManagementStatusRepository->save($omStatus);
        } catch (\Exception $exception) {
            $this->logManager->debug(
                $exception,
                [
                    'extra' => [
                        'payment_id' => $payment->getId(),
                    ],
                ]
            );
        }

        if ($result->getStatus() == 'Created') {
            if ($result->getPaymentTransactionId()) {
                $payment->setTransactionId($result->getPaymentTransactionId());
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Unable to capture payment for this order.')
            );
        }
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return void
     * @throws \Exception
     */
    public function captureByShipment($shipment)
    {
        if (!$this->qliroConfig->shouldCaptureOnShipment()) {
            return;
        }

        $this->shipmentMarkItemsAsShippedRequestBuilder->setShipment($shipment);
        $request = $this->shipmentMarkItemsAsShippedRequestBuilder->create();

        if (count($request->getOrderItems()) == 0) {
            return;
        }

        $result = $this->orderManagementApi->markItemsAsShipped($request);

        try {
            /** @var \Qliro\QliroOne\Model\OrderManagementStatus $omStatus */
            $omStatus = $this->orderManagementStatusInterfaceFactory->create();

            $omStatus->setRecordId($shipment->getId());
            $omStatus->setRecordType(OrderManagementStatusInterface::RECORD_TYPE_SHIPMENT);
            $omStatus->setTransactionId($result->getPaymentTransactionId());
            $omStatus->setTransactionStatus(QliroOrderManagementStatusInterface::STATUS_CREATED);
            $omStatus->setNotificationStatus(OrderManagementStatus::NOTIFICATION_STATUS_DONE);
            $omStatus->setMessage('Capture Requested for Shipment');

            /** @var \Magento\Sales\Model\Order $order */
            $order = $shipment->getOrder();
            $link = $this->linkRepository->getByOrderId($order->getId());
            $omStatus->setQliroOrderId($link->getQliroOrderId());

            $this->orderManagementStatusRepository->save($omStatus);
        } catch (\Exception $exception) {
            $this->logManager->debug(
                $exception,
                [
                    'extra' => [
                        'shipment_id' => $shipment->getId(),
                    ],
                ]
            );
        }

        if ($result->getStatus() != 'Created') {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Unable to mark items as shipped.')
            );
        }
    }

    /**
     * Handles Order Management Status Transaction notifications
     *
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @return \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatusResponse
     */
    public function handleTransactionStatus($qliroOrderManagementStatus)
    {
        $qliroOrderId = $qliroOrderManagementStatus->getOrderId();

        try {
            $link = $this->linkRepository->getByQliroOrderId($qliroOrderId);
            $this->logManager->setMerchantReference($link->getReference());

            $orderId = $link->getOrderId();

            if (empty($orderId)) {
                /* Should not happen, but if it does, respond with this to stop new notifications */
                return $this->qliroOrderManagementStatusRespond(
                    QliroOrderManagementStatusResponseInterface::RESPONSE_ORDER_NOT_FOUND
                );
            } elseif ($this->updateTransactionStatus($qliroOrderManagementStatus)) {
                return $this->qliroOrderManagementStatusRespond(
                    QliroOrderManagementStatusResponseInterface::RESPONSE_RECEIVED
                );
            }
        } catch (NoSuchEntityException $exception) {
            /* No more qliro notifications should be sent */
            return $this->qliroOrderManagementStatusRespond(
                QliroOrderManagementStatusResponseInterface::RESPONSE_RECEIVED
            );
        } catch (\Exception $exception) {
            $this->logManager->critical(
                $exception,
                [
                    'extra' => [
                        'qliro_order_id' => $qliroOrderId,
                    ],
                ]
            );

            return $this->qliroOrderManagementStatusRespond(
                QliroOrderManagementStatusResponseInterface::RESPONSE_ORDER_NOT_FOUND
            );
        }
    }

    /**
     * Get Admin Qliro order after it was already placed
     *
     * @param int $qliroOrderId
     * @return \Qliro\QliroOne\Api\Data\AdminOrderInterface
     */
    public function getAdminQliroOrder($qliroOrderId)
    {
        $qliroOrder = null; // Placeholder, QliroOne order will never be returned as null

        try {
            $link = $this->linkRepository->getByQliroOrderId($qliroOrderId);
            $this->logManager->setMerchantReference($link->getReference());
            $qliroOrder = $this->orderManagementApi->getOrder($qliroOrderId);
        } catch (\Exception $exception) {
            $this->logManager->critical(
                $exception,
                [
                    'extra' => [
                        'qliro_order_id' => isset($link) ? $link->getOrderId() : $qliroOrderId,
                    ],
                ]
            );
        }

        return $qliroOrder;
    }

    /**
     * If a transaction is received that is of same type as previou, same transaction id and marked as handled, it does
     * not have to be handled, since it was done already the first time it arrived.
     * Reply true when properly handled
     *
     * @param \Qliro\QliroOne\Model\Notification\QliroOrderManagementStatus $qliroOrderManagementStatus
     * @return bool
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function updateTransactionStatus($qliroOrderManagementStatus)
    {
        $result = false;

        try {
            $qliroOrderId = $qliroOrderManagementStatus->getOrderId();

            /** @var \Qliro\QliroOne\Model\OrderManagementStatus $omStatus */
            $omStatus = $this->orderManagementStatusInterfaceFactory->create();
            $omStatus->setTransactionId($qliroOrderManagementStatus->getPaymentTransactionId());
            $omStatus->setTransactionStatus($qliroOrderManagementStatus->getStatus());
            $omStatus->setQliroOrderId($qliroOrderId);
            $omStatus->setMessage('Notification update');

            $handleTransaction = true;

            try {
                /** @var \Qliro\QliroOne\Model\OrderManagementStatus $omStatusParent */
                $omStatusParent = $this->orderManagementStatusRepository->getParent(
                    $qliroOrderManagementStatus->getPaymentTransactionId()
                );

                if ($omStatusParent) {
                    $omStatus->setRecordId($omStatusParent->getRecordId());
                    $omStatus->setRecordType($omStatusParent->getRecordType());
                }

                /** @var \Qliro\QliroOne\Model\OrderManagementStatus $omStatusPrevious */
                $omStatusPrevious = $this->orderManagementStatusRepository->getPrevious(
                    $qliroOrderManagementStatus->getPaymentTransactionId()
                );

                if ($omStatusPrevious) {
                    if ($omStatus->getTransactionStatus() == $omStatusPrevious->getTransactionStatus()) {
                        $handleTransaction = false;
                    }
                }
            } catch (\Exception $exception) {
                $this->logManager->debug(
                    $exception,
                    [
                        'extra' => [
                            'qliro_order_id' => $qliroOrderId,
                            'transaction_id' => $omStatus->getTransactionId(),
                            'transaction_status' => $omStatus->getTransactionStatus(),
                            'record_type' => $omStatus->getRecordType(),
                            'record_id' => $omStatus->getRecordId(),
                        ],
                    ]
                );
            }

            if ($handleTransaction) {
                if ($this->lock->lock($qliroOrderId)) {
                    $omStatus->setNotificationStatus(OrderManagementStatus::NOTIFICATION_STATUS_NEW);
                    $this->orderManagementStatusRepository->save($omStatus);
                    if ($this->statusUpdateHandlerPool->handle($qliroOrderManagementStatus, $omStatus)) {
                        $omStatus->setNotificationStatus(OrderManagementStatus::NOTIFICATION_STATUS_DONE);
                    }
                } else {
                    $omStatus->setMessage('Skipped due to lock');
                    $omStatus->setNotificationStatus(OrderManagementStatus::NOTIFICATION_STATUS_SKIPPED);
                }
            } else {
                $omStatus->setNotificationStatus(OrderManagementStatus::NOTIFICATION_STATUS_SKIPPED);
            }

            $this->orderManagementStatusRepository->save($omStatus);
        } catch (\Exception $exception) {
            $logData = [
                'qliro_order_id' => $qliroOrderId ?? null,
            ];

            if (isset($omStatus)) {
                $logData = array_merge($logData, [
                    'transaction_id' => $omStatus->getTransactionId(),
                    'transaction_status' => $omStatus->getTransactionStatus(),
                    'record_type' => $omStatus->getRecordType(),
                    'record_id' => $omStatus->getRecordId(),
                ]);
            }

            $this->logManager->critical(
                $exception,
                [
                    'extra' => $logData,
                ]
            );

            if (isset($omStatus) && $omStatus && $omStatus->getId()) {
                $omStatus->setNotificationStatus(OrderManagementStatus::NOTIFICATION_STATUS_ERROR);
                $this->orderManagementStatusRepository->save($omStatus);
            }

            throw new TerminalException('Qliro Management Status failure', null, $exception);
        } finally {
            if (isset($qliroOrderId)) {
                $this->lock->unlock($qliroOrderId);
            }
        }

        return $result;
    }

    /**
     * Add information regarding this purchase to Quote, which will transfer to Order
     *
     * @param \Qliro\QliroOne\Api\Data\LinkInterface $link
     * @param \Qliro\QliroOne\Api\Data\QliroOrderPaymentMethodInterface $paymentMethod
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addAdditionalInfoToQuote($link, $paymentMethod)
    {
        $payment = $this->getQuote()->getPayment();
        $payment->setAdditionalInformation(Config::QLIROONE_ADDITIONAL_INFO_QLIRO_ORDER_ID, $link->getQliroOrderId());
        $payment->setAdditionalInformation(Config::QLIROONE_ADDITIONAL_INFO_REFERENCE, $link->getReference());

        if ($paymentMethod) {
            $payment->setAdditionalInformation(
                Config::QLIROONE_ADDITIONAL_INFO_PAYMENT_METHOD_CODE,
                $paymentMethod->getPaymentTypeCode()
            );

            $payment->setAdditionalInformation(
                Config::QLIROONE_ADDITIONAL_INFO_PAYMENT_METHOD_NAME,
                $paymentMethod->getPaymentMethodName()
            );
        }
    }

    /**
     * Get a link for the current quote
     *
     * @return \Qliro\QliroOne\Api\Data\LinkInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function getLinkFromQuote()
    {
        $quote = $this->getQuote();
        $quoteId = $quote->getEntityId();

        try {
            $link = $this->linkRepository->getByQuoteId($quoteId);
        } catch (NoSuchEntityException $exception) {
            /** @var \Qliro\QliroOne\Api\Data\LinkInterface $link */
            $link = $this->linkFactory->create();
            $link->setRemoteIp($this->helper->getRemoteIp());
            $link->setIsActive(true);
            $link->setQuoteId($quoteId);
        }

        if ($link->getQliroOrderId()) {
            $this->updateQliroOrder($link->getQliroOrderId());
        } else {
            $this->logManager->debug('create new qliro order');
            $orderReference = $this->generateOrderReference();
            $this->logManager->setMerchantReference($orderReference);

            $request = $this->createRequestBuilder->setQuote($quote)->create();
            $request->setMerchantReference($orderReference);

            try {
                $orderId = $this->merchantApi->createOrder($request);
            } catch (\Exception $exception) {
                $orderId = null;
            }

            $hash = $this->generateUpdateHash($quote);
            $link->setQuoteSnapshot($hash);

            $link->setIsActive(true);
            $link->setReference($orderReference);
            $link->setQliroOrderId($orderId);
            $this->linkRepository->save($link);
        }

        return $link;
    }

    /**
     * Generate a hash for quote content comparison
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return string
     */
    private function generateUpdateHash($quote)
    {
        $request = $this->updateRequestBuilder->setQuote($quote)->create();
        $data = $this->containerMapper->toArray($request);
        unset($data['AvailableShippingMethods']);
        sort($data);

        try {
            $serializedData = $this->json->serialize($data);
        } catch (\InvalidArgumentException $exception) {
            $serializedData = null;
        }

        $hash = $serializedData ? md5($serializedData) : null;

        $this->logManager->debug(
            sprintf('generateUpdateHash: %s', $hash),
            ['extra' => var_export($data, true)]
        );

        return $hash;
    }

    /**
     * Generate a QliroOne unique order reference
     *
     * @return string
     */
    private function generateOrderReference()
    {
        $quote = $this->getQuote();
        $hash = $this->hashResolver->resolveHash($quote);
        $this->validateHash($hash);
        $hashLength = self::REFERENCE_MIN_LENGTH;

        do {
            $isUnique = false;
            $shortenedHash = substr($hash, 0, $hashLength);

            try {
                $this->linkRepository->getByReference($shortenedHash);

                if ((++$hashLength) > HashResolverInterface::HASH_MAX_LENGTH) {
                    $hash = $this->hashResolver->resolveHash($quote);
                    $this->validateHash($hash);
                    $hashLength = self::REFERENCE_MIN_LENGTH;
                }
            } catch (NoSuchEntityException $exception) {
                $isUnique = true;
            }
        } while (!$isUnique);

        return $shortenedHash;
    }

    /**
     * Fetch the HTML snippet from the current QliroOne order
     *
     * @return string
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Qliro\QliroOne\Model\Exception\TerminalException
     */
    private function doFetchHtmlSnippet()
    {
        return $this->getQliroOrder()->getOrderHtmlSnippet();
    }

    /**
     * Fetch a quote, either from customer session or by specific ID.
     * Note that this method is utilyzing Object Manager directly. Sadly, it must be done this way,
     * due to poor Magento design which let's Magento crash if some other class is trying to initialize Session again,
     * once it was initialized before. Particularly, this will be fatal if you try to instantiat this method
     *
     * @return \Magento\Quote\Model\Quote
     */
    private function getQuote()
    {
        if (!($this->quote instanceof CartInterface)) {
            throw new \LogicException('Quote must be set before it is fetched.');
        }

        return $this->quote;
    }

    /**
     * Recalculate the quote, its totals, it's addresses and shipping rates, then saving quote
     *
     * @throws \Exception
     */
    private function recalculateAndSaveQuote()
    {
        $data['method'] = QliroOne::PAYMENT_METHOD_CHECKOUT_CODE;

        $quote = $this->getQuote();
        $customer = $quote->getCustomer();
        $shippingAddress = $quote->getShippingAddress();
        $billingAddress = $quote->getBillingAddress();

        if ($quote->isVirtual()) {
            $billingAddress->setPaymentMethod($data['method']);
        } else {
            $shippingAddress->setPaymentMethod($data['method']);
        }

        $billingAddress->save();

        if (!$quote->isVirtual()) {
            $shippingAddress->save();
        }

        $quote->assignCustomerWithAddressChange($customer, $billingAddress, $shippingAddress);
        $quote->setTotalsCollectedFlag(false);

        if (!$quote->isVirtual()) {
            $shippingAddress->setCollectShippingRates(true)->collectShippingRates()->save();
        }

        $extensionAttributes = $quote->getExtensionAttributes();

        if (!empty($extensionAttributes)) {
            $shippingAssignments = $extensionAttributes->getShippingAssignments();

            foreach ($shippingAssignments as $assignment) {
                $assignment->getShipping()->setMethod($shippingAddress->getShippingMethod());
            }
        }
        $quote->collectTotals();

        $payment = $quote->getPayment();
        $payment->importData($data);

        $shippingAddress->save();
        $this->quoteRepository->save($quote);
    }

    /**
     * Validate hash against QliroOne order merchant reference requirements
     *
     * @param string $hash
     */
    private function validateHash($hash)
    {
        if (!preg_match(HashResolverInterface::VALIDATE_MERCHANT_REFERENCE, $hash)) {
            throw new \DomainException(sprintf('Merchant reference \'%s\' will not be accepted by Qliro', $hash));
        }
    }

    /**
     * Check if QliroOne order can be updated
     *
     * @param string $hash
     * @param \Qliro\QliroOne\Api\Data\LinkInterface $link
     * @return bool
     */
    private function canUpdateOrder($hash, LinkInterface $link)
    {
        return empty($this->getQuote()->getShippingAddress()->getShippingMethod()) || $link->getQuoteSnapshot() !== $hash;
    }

    /**
     * @param string $result
     * @return mixed
     */
    private function checkoutStatusRespond($result)
    {
        return $this->checkoutStatusResponseFactory->create()->setCallbackResponse($result);
    }

    /**
     * @param string $result
     * @return mixed
     */
    private function qliroOrderManagementStatusRespond($result)
    {
        return $this->qliroOrderManagementStatusResponseFactory->create()->setCallbackResponse($result);
    }

    /**
     * Act on the order based on the qliro order status
     * It can be one of:
     * - Completed - the order can be shipped
     * - OnHold - review of buyer require more time
     * - Refused - deny the purchase
     *
     * @param Order $order
     * @return bool
     */
    private function applyQliroOrderStatus($order)
    {
        $orderId = $order->getId();
        try {
            $link = $this->linkRepository->getByOrderId($orderId);

            switch ($link->getQliroOrderStatus()) {
                case CheckoutStatusInterface::STATUS_COMPLETED:
                    $this->applyOrderState($order, Order::STATE_NEW);

                    if ($order->getCanSendNewEmailFlag() && !$order->getEmailSent()) {
                        try {
                            $this->orderSender->send($order);
                        } catch (\Exception $exception) {
                            $this->logManager->critical(
                                $exception,
                                [
                                    'extra' => [
                                        'order_id' => $orderId,
                                    ],
                                ]
                            );
                        }
                    }
                    break;

                case CheckoutStatusInterface::STATUS_ONHOLD:
                    $this->applyOrderState($order, Order::STATE_PAYMENT_REVIEW);
                    break;

                case CheckoutStatusInterface::STATUS_REFUSED:
                    // Deactivate link regardless of if the upcoming order cancellation successful or not
                    $link->setIsActive(false);
                    $link->setMessage(sprintf('Order #%s marked as canceled', $order->getIncrementId()));
                    $this->linkRepository->save($link);
                    $this->applyOrderState($order, Order::STATE_NEW);

                    if ($order->canCancel()) {
                        $order->cancel();
                        $this->orderRepository->save($order);
                    }

                    break;

                case CheckoutStatusInterface::STATUS_IN_PROCESS:
                default:
                    return false;
            }

            return true;
        } catch (\Exception $exception) {
            $this->logManager->critical(
                $exception,
                [
                    'extra' => [
                        'order_id' => $orderId,
                    ],
                ]
            );

            return false;
        }
    }

    /**
     * Apply a proper state with its default status to the order
     *
     * @param \Magento\Sales\Model\Order $order
     * @param string $state
     */
    private function applyOrderState(Order $order, $state)
    {
        $status = Order::STATE_NEW === $state
            ? $this->qliroConfig->getOrderStatus()
            : $order->getConfig()->getStateDefaultStatus($state);

        $order->setState($state);
        $order->setStatus($status);
        $this->orderRepository->save($order);
    }

    /**
     * Special case is processed here:
     * When the QliroOne order is not found, among active links, but push notification updates
     * status to "Completed", we want to find an inactive link and cancel such QliroOne order,
     * because Magento has previously failed creating corresponding order for it.
     *
     * @param \Qliro\QliroOne\Api\Data\CheckoutStatusInterface $checkoutStatus
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function handleOrderCancelationIfRequired(CheckoutStatusInterface $checkoutStatus)
    {
        $qliroOrderId = $checkoutStatus->getOrderId();

        if ($checkoutStatus->getStatus() === CheckoutStatusInterface::STATUS_COMPLETED) {
            $link = $this->linkRepository->getByQliroOrderId($qliroOrderId, false);

            try {
                $this->logManager->setMerchantReference($link->getReference());
                $link->setQliroOrderStatus($checkoutStatus->getStatus());
                $this->cancelQliroOrder($link->getQliroOrderId());
                $link->setMessage(sprintf('Requested to cancel QliroOne order #%s', $link->getQliroOrderId()));
            } catch (TerminalException $exception) {
                $link->setMessage(sprintf('Failed to cancel QliroOne order #%s', $link->getQliroOrderId()));
            }

            $this->linkRepository->save($link);
        }
    }
}
