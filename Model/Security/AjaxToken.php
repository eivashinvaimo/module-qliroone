<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Security;

use Magento\Quote\Model\Quote;

/**
 * AJAX Token handling class
 */
class AjaxToken extends CallbackToken
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * Set quote to properly calculate the token
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Qliro\QliroOne\Model\Security\AjaxToken
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;
        return $this;
    }

    /**
     * Get an expiration timestamp for the token
     *
     * @return int
     */
    public function getExpirationTimestamp()
    {
        return strtotime('+1 hour');
    }

    /**
     * Add quote ID to the token
     *
     * @return mixed
     */
    public function getAdditionalData()
    {
        return $this->quote instanceof Quote ? (int)$this->quote->getId() : null;
    }
}
