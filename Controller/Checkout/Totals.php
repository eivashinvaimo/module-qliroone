<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOneController\Checkout;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Json\Helper\Data;
use Magento\Quote\Api\CartRepositoryInterface;

class Totals extends Action
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var JsonFactory
     */
    protected $resultJson;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Checkout Totals Ajax Controller constructor.
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param Data $helper
     * @param JsonFactory $resultJson
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        Data $helper,
        JsonFactory $resultJson,
        CartRepositoryInterface $quoteRepository
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->helper = $helper;
        $this->resultJson = $resultJson;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Trigger to re-calculate the collect Totals
     *
     * @return bool
     */
    public function execute()
    {
        $response = [
            'errors' => false,
            'message' => ''
        ];

        try {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->get($this->checkoutSession->getQuoteId());

            /** @var array $payment */
            $payment = $this->helper->jsonDecode($this->getRequest()->getContent());
            $quote->getPayment()->setMethod($payment['payment']);
            $quote->collectTotals();
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            $response = [
                'errors' => true,
                'message' => $e->getMessage()
            ];
        }

        /** @var \Magento\Framework\Controller\Result\Raw $resultJson */
        $resultJson = $this->resultJson->create();

        return $resultJson->setData($response);
    }
}
