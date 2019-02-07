<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Qliro\QliroOne\Block\Info;

class QliroOne extends AbstractInfo
{
    /**
     * QliroOne info template
     *
     * @var string
     */
    protected $_template = 'Qliro_QliroOne::info/qliroone.phtml';

    /**
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('Qliro_QliroOne::info/pdf/qliroone.phtml');
        return $this->toHtml();
    }

    /**
     * @return string
     */
    public function getQliroOrderId()
    {
        return $this->getInfo()->getAdditionalInformation('qliro_order_id');
    }

    /**
     * @return string
     */
    public function getQliroReference()
    {
        return $this->getInfo()->getAdditionalInformation('qliro_reference');
    }
}
