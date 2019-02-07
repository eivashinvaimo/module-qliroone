<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Qliro\QliroOne\Model\Newsletter;

/*
 * derived from Magento\Newsletter\Controller\Subscriber\NewAction.php
 */

use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Message\ManagerInterface;
use Qliro\QliroOne\Api\SubscriptionInterface;
use Qliro\QliroOne\Model\Logger\Manager as LogManager;
use Magento\Store\Model\ScopeInterface;
use Magento\Sales\Model\Order\Address;

/**
 * Class Subscription
 */
class Subscription implements SubscriptionInterface
{
    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Customer\Model\Url
     */
    private $customerUrl;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Qliro\QliroOne\Model\Logger\Manager
     */
    private $logManager;

    /**
     * Initialize dependencies.
     *
     * @param SubscriberFactory $subscriberFactory
     * @param Session $customerSession
     * @param CustomerUrl $customerUrl
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Qliro\QliroOne\Model\Logger\Manager $logManager
     */
    public function __construct(
        SubscriberFactory $subscriberFactory,
        Session $customerSession,
        CustomerUrl $customerUrl,
        ScopeConfigInterface $scopeConfig,
        ManagerInterface $messageManager,
        LogManager $logManager
    ) {
        $this->subscriberFactory = $subscriberFactory;
        $this->customerSession = $customerSession;
        $this->customerUrl = $customerUrl;
        $this->scopeConfig = $scopeConfig;
        $this->messageManager = $messageManager;
        $this->logManager = $logManager;
    }

    /**
     * Validates that if the current user is a guest, that they can subscribe to a newsletter.
     *
     * @param int $storeId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    private function validateGuestSubscription($storeId)
    {
        if (
            $this->scopeConfig->getValue(Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG, ScopeInterface::SCOPE_STORE, $storeId) != 1
            && !$this->customerSession->isLoggedIn()
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'Sorry, but the administrator denied subscription for guests. Please <a href="%1">register</a>.',
                    $this->customerUrl->getRegisterUrl()
                )
            );
        }
    }

    /**
     * Validates the format of the email address
     *
     * @param string $email
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    private function validateEmailFormat($email)
    {
        if (!\filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a valid email address.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function addSubscription($email, $storeId)
    {
        try {
            $this->validateEmailFormat($email);
            $this->validateGuestSubscription($storeId);

            $subscriber = $this->subscriberFactory->create()->loadByEmail($email);
            if ($subscriber->getId() || $subscriber->getSubscriberStatus() != Subscriber::STATUS_SUBSCRIBED) {
                $status = $this->subscriberFactory->create()->subscribe($email);
                $this->logManager->info('Added {email} as subscriber', ['email' => $email]);
                if ($status == Subscriber::STATUS_NOT_ACTIVE) {
                    $this->messageManager->addSuccessMessage(__('The confirmation request has been sent.'));
                } else {
                    $this->messageManager->addSuccessMessage(__('Thank you for your subscription.'));
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->logManager->critical(
                $exception,
                [
                    'extra' => [
                        'email' => $email,
                        'storeId' => $storeId
                    ],
                ]
            );
            $this->messageManager->addExceptionMessage(
                $exception,
                __('There was a problem with the subscription: %1', $exception->getMessage())
            );
        } catch (\Exception $exception) {
            $this->logManager->critical(
                $exception,
                [
                    'extra' => [
                        'email' => $email,
                        'storeId' => $storeId
                    ],
                ]
            );
            $this->messageManager->addExceptionMessage($exception, __('Something went wrong with the subscription.'));
        }
    }
}
