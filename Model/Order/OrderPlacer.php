<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Order;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Event\ManagerInterface;

/**
 * Magento order placer class
 */
class OrderPlacer
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    private $cartManagement;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Inject dependencies
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagement
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        CartManagementInterface $cartManagement,
        OrderRepositoryInterface $orderRepository,
        ManagerInterface $eventManager,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->cartManagement = $cartManagement;
        $this->orderRepository = $orderRepository;
        $this->eventManager = $eventManager;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Place order should be very small, all validations and updates should be done before calling this
     * Onepage::METHOD_REGISTER should not be possible to get
     *
     * @param Quote $quote
     * @return \Magento\Sales\Model\Order
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function place($quote)
    {
        switch ($this->getCheckoutMethod($quote)) {
            case Onepage::METHOD_GUEST:
                $this->prepareGuestQuote($quote);
                break;
            default:
                $this->prepareCustomerQuote($quote);
                break;
        }
        $this->quoteRepository->save($quote);

        $orderId = $this->cartManagement->placeOrder($quote->getId());

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->orderRepository->get($orderId);

        return $order;
    }

    /**
     * Get quote checkout method
     * No need to test for guest, as it's impossible to get to checkout if that's disallowed.
     *
     * @param Quote $quote
     * @return string
     */
    private function getCheckoutMethod($quote)
    {
        if ($quote->getCustomerId()) {
            $quote->setCheckoutMethod(Onepage::METHOD_CUSTOMER);
            return $quote->getCheckoutMethod();
        }
        if (!$quote->getCheckoutMethod()) {
            $quote->setCheckoutMethod(Onepage::METHOD_GUEST);
        }

        return $quote->getCheckoutMethod();
    }

    /**
     * Prepare quote for guest checkout order submit
     *
     * @param Quote $quote
     * @return $this
     */
    private function prepareGuestQuote($quote)
    {
        $quote->setCustomerId(null)
            ->setCustomerEmail($quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(\Magento\Customer\Api\Data\GroupInterface::NOT_LOGGED_IN_ID);
        return $this;
    }

    /**
     * Prepare quote for customer order submit
     *
     * @param Quote $quote
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function prepareCustomerQuote($quote)
    {
        $billing = $quote->getBillingAddress();
        $shipping = $quote->isVirtual() ? null : $quote->getShippingAddress();

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->customerRepository->getById($quote->getCustomerId());
        $hasDefaultBilling = (bool)$customer->getDefaultBilling();
        $hasDefaultShipping = (bool)$customer->getDefaultShipping();

        if ($shipping && !$shipping->getSameAsBilling() &&
            (!$shipping->getCustomerId() || $shipping->getSaveInAddressBook())
        ) {
            $shippingAddress = $shipping->exportCustomerAddress();
            if (!$hasDefaultShipping) {
                //Make provided address as default shipping address
                $shippingAddress->setIsDefaultShipping(true);
                $hasDefaultShipping = true;
            }
            $quote->addCustomerAddress($shippingAddress);
            $shipping->setCustomerAddressData($shippingAddress);
        }

        if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
            $billingAddress = $billing->exportCustomerAddress();
            if (!$hasDefaultBilling) {
                //Make provided address as default shipping address
                if (!$hasDefaultShipping) {
                    //Make provided address as default shipping address
                    $billingAddress->setIsDefaultShipping(true);
                }
                $billingAddress->setIsDefaultBilling(true);
            }
            $quote->addCustomerAddress($billingAddress);
            $billing->setCustomerAddressData($billingAddress);
        }
    }

}
