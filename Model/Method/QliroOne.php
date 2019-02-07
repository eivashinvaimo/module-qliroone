<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Qliro\QliroOne\Model\Method;

use Magento\Payment\Model\Method\Adapter;
use Magento\Framework\DataObject;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Payment\Model\InfoInterface;

/**
 * QliroOne payment method class
 */
class QliroOne implements MethodInterface
{
    const PAYMENT_METHOD_CHECKOUT_CODE = 'qliroone';
    const PAYMENT_METHOD_FORM_BLOCK_TYPE = 'Qliro\QliroOne\Block\Form\QliroOne';
    const PAYMENT_METHOD_INFO_BLOCK_TYPE = 'Qliro\QliroOne\Block\Info\QliroOne';

    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * Inject dependencies
     *
     * @param Adapter $adapter
     */
    public function __construct(
        Adapter $adapter
    ) {
        $this->adapter = $adapter;
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return $this->adapter->getCode();
    }

    /**
     * @inheritdoc
     */
    public function getFormBlockType()
    {
        return $this->adapter->getFormBlockType();
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->adapter->getTitle();
    }

    /**
     * @inheritdoc
     */
    public function setStore($storeId)
    {
        $this->adapter->setStore($storeId);
    }

    /**
     * @inheritdoc
     */
    public function getStore()
    {
        return $this->adapter->getStore();
    }

    /**
     * @inheritdoc
     */
    public function canOrder()
    {
        return $this->adapter->canOrder();
    }

    /**
     * @inheritdoc
     */
    public function canAuthorize()
    {
        return $this->adapter->canAuthorize();
    }

    /**
     * @inheritdoc
     */
    public function canCapture()
    {
        return $this->adapter->canCapture();
    }

    /**
     * @inheritdoc
     */
    public function canCapturePartial()
    {
        return $this->adapter->canCapturePartial();
    }

    /**
     * @inheritdoc
     */
    public function canCaptureOnce()
    {
        return $this->adapter->canCaptureOnce();
    }

    /**
     * @inheritdoc
     */
    public function canRefund()
    {
        return $this->adapter->canRefund();
    }

    /**
     * @inheritdoc
     */
    public function canRefundPartialPerInvoice()
    {
        return $this->adapter->canRefundPartialPerInvoice();
    }

    /**
     * @inheritdoc
     */
    public function canVoid()
    {
        return $this->adapter->canVoid();
    }

    /**
     * @inheritdoc
     */
    public function canUseInternal()
    {
        return $this->adapter->canUseInternal();
    }

    /**
     * @inheritdoc
     */
    public function canUseCheckout()
    {
        return $this->adapter->canUseCheckout();
    }

    /**
     * @inheritdoc
     */
    public function canEdit()
    {
        return $this->adapter->canEdit();
    }

    /**
     * @inheritdoc
     */
    public function canFetchTransactionInfo()
    {
        return $this->adapter->canFetchTransactionInfo();
    }

    /**
     * @inheritdoc
     */
    public function fetchTransactionInfo(InfoInterface $payment, $transactionId)
    {
        return $this->adapter->fetchTransactionInfo($payment, $transactionId);
    }

    /**
     * @inheritdoc
     */
    public function isGateway()
    {
        return $this->adapter->isGateway();
    }

    /**
     * @inheritdoc
     */
    public function isOffline()
    {
        return $this->adapter->isOffline();
    }

    /**
     * @inheritdoc
     */
    public function isInitializeNeeded()
    {
        return $this->adapter->isInitializeNeeded();
    }

    /**
     * @inheritdoc
     */
    public function canUseForCountry($country)
    {
        return $this->adapter->canUseForCountry($country);
    }

    /**
     * @inheritdoc
     */
    public function canUseForCurrency($currencyCode)
    {
        return $this->adapter->canUseForCurrency($currencyCode);
    }

    /**
     * @inheritdoc
     */
    public function getInfoBlockType()
    {
        return $this->adapter->getInfoBlockType();
    }

    /**
     * @inheritdoc
     */
    public function getInfoInstance()
    {
        return $this->adapter->getInfoInstance();
    }

    /**
     * @inheritdoc
     */
    public function setInfoInstance(InfoInterface $info)
    {
        $this->adapter->setInfoInstance($info);
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return $this->adapter->validate();
    }

    /**
     * @inheritdoc
     */
    public function order(InfoInterface $payment, $amount)
    {
        throw new \Exception("order - Stop\n");
        return $this->adapter->order($payment, $amount);
    }

    /**
     * @inheritdoc
     */
    public function authorize(InfoInterface $payment, $amount)
    {
        throw new \Exception("authorize - Stop\n");
        return $this->adapter->authorize($payment, $amount);
    }

    /**
     * @inheritdoc
     */
    public function capture(InfoInterface $payment, $amount)
    {
        return $this->adapter->capture($payment, $amount);
    }

    /**
     * @inheritdoc
     */
    public function refund(InfoInterface $payment, $amount)
    {
        throw new \Exception("refund - Stop\n");
        return $this->adapter->refund($payment, $amount);
    }

    /**
     * @inheritdoc
     */
    public function cancel(InfoInterface $payment)
    {
        return $this->adapter->cancel($payment);
    }

    /**
     * @inheritdoc
     */
    public function void(InfoInterface $payment)
    {
        return $this->adapter->void($payment);
    }

    /**
     * @inheritdoc
     */
    public function canReviewPayment()
    {
        //throw new \Exception("canReviewPayment - Stop\n");
        return $this->adapter->canReviewPayment();
    }

    /**
     * @inheritdoc
     */
    public function acceptPayment(InfoInterface $payment)
    {
        //throw new \Exception("acceptPayment - Stop\n");
        return $this->adapter->acceptPayment($payment);
    }

    /**
     * @inheritdoc
     */
    public function denyPayment(InfoInterface $payment)
    {
        //throw new \Exception("denyPayment - Stop\n");
        return $this->adapter->denyPayment($payment);
    }

    /**
     * @inheritdoc
     */
    public function getConfigData($field, $storeId = null)
    {
        return $this->adapter->getConfigData($field, $storeId);
    }

    /**
     * @inheritdoc
     */
    public function assignData(DataObject $data)
    {
        return $this->adapter->assignData($data);
    }

    /**
     * @inheritdoc
     */
    public function isAvailable(CartInterface $quote = null)
    {
        return $this->adapter->isAvailable($quote);
    }

    /**
     * @inheritdoc
     */
    public function isActive($storeId = null)
    {
        return $this->adapter->isActive($storeId);
    }

    /**
     * @inheritdoc
     */
    public function initialize($paymentAction, $stateObject)
    {
        return $this->adapter->initialize($paymentAction, $stateObject);
    }

    /**
     * @inheritdoc
     */
    public function getConfigPaymentAction()
    {
        return $this->adapter->getConfigPaymentAction();
    }
}
