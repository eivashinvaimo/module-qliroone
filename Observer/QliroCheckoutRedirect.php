<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Manager as EventManager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Url;
use Qliro\QliroOne\Model\Config;

class QliroCheckoutRedirect implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Event\Manager
     */
    private $manager;

    /**
     * @var \Magento\Framework\Url
     */
    private $url;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var \Qliro\QliroOne\Model\Config
     */
    private $qliroConfig;

    /**
     * Inject dependencies
     *
     * @param \Magento\Framework\Event\Manager $manager
     * @param \Magento\Framework\Url $urlModel
     * @param \Magento\Checkout\Model\Session $session
     * @param \Qliro\QliroOne\Model\Config $qliroConfig
     */
    public function __construct(
        EventManager $manager,
        Url $urlModel,
        Session $session,
        Config $qliroConfig
    ) {
        $this->url = $urlModel;
        $this->manager = $manager;
        $this->session = $session;
        $this->qliroConfig = $qliroConfig;
    }

    /**
     * Override the redirect to checkout but make it possible to control this override in custom extensions
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $state = new DataObject();

        $state->setData([
            'redirect_url' => $this->url->getRouteUrl('checkout/qliro'),
        ]);

        $this->manager->dispatch(
            'qliroone_override_load_checkout',
            [
                'state' => $state,
                'checkout_observer' => $observer,
            ]
        );

        $mustEnable = $state->getMustEnable();
        $mustDisable = $state->getMustDisable();
        $qliroOverride = $this->session->getQliroOverride();


        if ($mustEnable || (!$mustDisable&& !$qliroOverride && $this->qliroConfig->isActive())) {
            $observer->getControllerAction()
                ->getResponse()
                ->setRedirect($state->getRedirectUrl())
                ->sendResponse();
        }
    }
}
