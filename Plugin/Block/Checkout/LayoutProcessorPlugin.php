<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Plugin\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Qliro\QliroOne\Api\ManagementInterface;

/**
 * Checkout Layout Processor plugin class
 */
class LayoutProcessorPlugin
{
    /**
     * @var \Magento\Store\Api\Data\StoreInterface
     */
    private $store;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterface
     */
    private $customer;

    /**
     * @var ManagerInterface
     */
    private $manager;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var \Qliro\QliroOne\Api\ManagementInterface
     */
    private $qliroManagement;

    /**
     * Inject dependencies
     *
     * @param Session $session
     * @param ManagerInterface $manager
     * @param State $appState
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $config
     * @param \Qliro\QliroOne\Api\ManagementInterface $qliroManagement
     */
    public function __construct(
        Session $session,
        ManagerInterface $manager,
        State $appState,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $config,
        ManagementInterface $qliroManagement
    ) {
        $this->quote = $session->getQuote();
        $this->customer = $this->quote->getCustomer();
        $this->manager = $manager;
        $this->appState = $appState;
        $this->config = $config;
        $this->store = $storeManager->getStore();
        $this->qliroManagement = $qliroManagement;
    }

    /**
     * Alter the checkout configuration array to add binds for QliroOne OnePage checkout
     *
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $result
     * @return array
     */
    public function afterProcess(LayoutProcessor $subject, $result)
    {
        if (isset($result['components']['checkout']['children']['steps']['children']['qliroone-step'])) {
            $htmlSnippet = $this->generateHtmlSnippet();
            $result['components']['checkout']['children']['steps']['children']['qliroone-step']['html_snippet'] =
                $htmlSnippet;
        }

        return $result;
    }

    /**
     * Returns iframe snippet of checkout form
     *
     * @return string
     */
    private function generateHtmlSnippet()
    {
        return (string)$this->qliroManagement->setQuote($this->quote)->getHtmlSnippet();
    }
}
