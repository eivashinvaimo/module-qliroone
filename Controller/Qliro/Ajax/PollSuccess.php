<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Controller\Qliro\Ajax;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Qliro\QliroOne\Api\ManagementInterface;
use Qliro\QliroOne\Helper\Data;
use Qliro\QliroOne\Model\Config;
use Qliro\QliroOne\Model\Exception\FailToLockException;
use Qliro\QliroOne\Model\Exception\TerminalException;
use Qliro\QliroOne\Model\Logger\Manager;
use Qliro\QliroOne\Model\Quote\Agent;
use Qliro\QliroOne\Model\Security\AjaxToken;

/**
 * AJAX controller action class for triggering order placement and polling for success
 */
class PollSuccess extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Qliro\QliroOne\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Qliro\QliroOne\Model\Security\AjaxToken
     */
    private $ajaxToken;

    /**
     * @var \Qliro\QliroOne\Model\Config
     */
    private $qliroConfig;

    /**
     * @var \Qliro\QliroOne\Api\ManagementInterface
     */
    private $management;

    /**
     * @var \Qliro\QliroOne\Model\Logger\Manager
     */
    private $logManager;

    /**
     * @var \Qliro\QliroOne\Model\Quote\Agent
     */
    private $quoteAgent;

    /**
     * Inject dependnecies
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Qliro\QliroOne\Model\Config $qliroConfig
     * @param \Qliro\QliroOne\Helper\Data $dataHelper
     * @param \Qliro\QliroOne\Model\Security\AjaxToken $ajaxToken
     * @param \Qliro\QliroOne\Api\ManagementInterface $management
     * @param \Qliro\QliroOne\Model\Quote\Agent $quoteAgent
     * @param \Qliro\QliroOne\Model\Logger\Manager $logManager
     */
    public function __construct(
        Context $context,
        Config $qliroConfig,
        Data $dataHelper,
        AjaxToken $ajaxToken,
        ManagementInterface $management,
        Agent $quoteAgent,
        Manager $logManager
    ) {
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
        $this->ajaxToken = $ajaxToken;
        $this->qliroConfig = $qliroConfig;
        $this->management = $management;
        $this->logManager = $logManager;
        $this->quoteAgent = $quoteAgent;
    }

    /**
     * Dispatch the action
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
        if (!$this->qliroConfig->isActive()) {
            return $this->dataHelper->sendPreparedPayload(
                [
                    'status' => 'FAILED',
                    'error' => (string)__('Qliro One is not active.')
                ],
                403,
                null,
                'AJAX:POLL_SUCCESS:ERROR_INACTIVE'
            );
        }

        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->getRequest();

        $quote = $this->quoteAgent->fetchRelevantQuote();
        $this->logManager->setMerchantReferenceFromQuote($quote);
        $this->ajaxToken->setQuote($quote);
        $this->management->setQuote($quote);

        if (!$quote) {
            return $this->dataHelper->sendPreparedPayload(
                [
                    'status' => 'FAILED',
                    'error' => (string)__('There cart was reset.')
                ],
                401,
                null,
                'AJAX:POLL_SUCCESS:ERROR_QUOTE'
            );
        }

        if (!$this->ajaxToken->verifyToken($request->getParam('token'))) {
            return $this->dataHelper->sendPreparedPayload(
                [
                    'status' => 'FAILED',
                    'error' => (string)__('Security token is incorrect.')
                ],
                401,
                null,
                'AJAX:POLL_SUCCESS:ERROR_TOKEN'
            );
        }

        $htmlSnippet = null;

        try {
            $orderIncrementId = $this->management->pollPlaceOrder();
            $htmlSnippet = $this->management->getHtmlSnippet();
            $this->quoteAgent->clear();
        } catch (TerminalException $exception) {
            if ($exception->getPrevious() instanceof FailToLockException) {
                return $this->dataHelper->sendPreparedPayload(
                    ['status' => 'PENDING'],
                    200,
                    null,
                    'AJAX:POLL_SUCCESS:PENDING'
                );
            }

            $this->quoteAgent->clear();

            return $this->dataHelper->sendPreparedPayload(
                [
                    'status' => 'FAILED',
                    'error' => (string)__('Order cannot be placed. Please try again, or contact our Customer Service.')
                ],
                400,
                null,
                'AJAX:POLL_SUCCESS:ERROR'
            );
        }

        return $this->dataHelper->sendPreparedPayload(
            [
                'status' => 'OK',
                'htmlSnippet' => $htmlSnippet,
                'orderIncrementId' => $orderIncrementId,
            ],
            200,
            null,
            'AJAX:POLL_SUCCESS'
        );
    }
}
