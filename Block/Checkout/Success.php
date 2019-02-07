<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Block\Checkout;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Qliro\QliroOne\Api\ManagementInterface;
use Qliro\QliroOne\Model\Config;
use Qliro\QliroOne\Model\Quote\Agent;
use Qliro\QliroOne\Model\Security\AjaxToken;

/**
 * QliroOne checkout success page main block class
 */
class Success extends Template
{
    /**
     * @var \Qliro\QliroOne\Api\ManagementInterface
     */
    private $qliroManagement;

    /**
     * @var \Qliro\QliroOne\Model\Security\AjaxToken
     */
    private $ajaxToken;

    /**
     * @var \Qliro\QliroOne\Model\Config
     */
    private $qliroConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Qliro\QliroOne\Model\Quote\Agent
     */
    private $quoteAgent;

    /**
     * Inject dependencies
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Qliro\QliroOne\Api\ManagementInterface $qliroManagement
     * @param \Qliro\QliroOne\Model\Security\AjaxToken $ajaxToken
     * @param \Qliro\QliroOne\Model\Config $qliroConfig
     * @param \Qliro\QliroOne\Model\Quote\Agent $quoteAgent
     * @param array $data
     */
    public function __construct(
        Context $context,
        ManagementInterface $qliroManagement,
        AjaxToken $ajaxToken,
        Config $qliroConfig,
        Agent $quoteAgent,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->qliroManagement = $qliroManagement;
        $this->ajaxToken = $ajaxToken;
        $this->qliroConfig = $qliroConfig;
        $this->quoteAgent = $quoteAgent;
        $this->storeManager = $context->getStoreManager();
    }

    /**
     * Get QliroOne final HTML snippet
     *
     * @return string
     */
    public function getHtmlSnippet()
    {
        $quote = $this->quoteAgent->fetchRelevantQuote();

        return $quote ? $this->qliroManagement->setQuote($quote)->getHtmlSnippet() : null;
    }

    /**
     * Get a URL for the polling script
     *
     * @return string
     */
    public function getPollSuccessUrl()
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore();

        $quote = $this->quoteAgent->fetchRelevantQuote();

        if ($quote) {
            $params = [
                '_query' => [
                    'token' => $this->ajaxToken->setQuote($quote)->getToken(),
                ]
            ];
        } else {
            $params = [];
        }

        return $store->getUrl('checkout/qliro_ajax/pollSuccess', $params);
    }

    /**
     * Check if debug mode is on
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->qliroConfig->isDebugMode();
    }
}
