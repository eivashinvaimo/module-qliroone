<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Model\Logger;

use Psr\Log\LoggerInterface;
use Qliro\QliroOne\Model\ResourceModel\LogRecord;
use Qliro\QliroOne\Api\LinkRepositoryInterface;

/**
 * Class Manage
 *
 * Provide a layer on top of the psr logger that adds our additional data, a tag and the process id.
 * The tag should be set as early as possibly; all logging after that will include the tag (merchant id,)
 * making it easier to filter the log in sequel pro to see only data relevant to one shopper.
 *
 */
class Manager
{
    /**
     * @var array
     */
    private $marks = [];

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $psrLogger;

    /**
     * @var string
     */
    private $merchantReference;

    /**
     * @var array
     */
    private $tags = [];

    /**
     * @var \Qliro\QliroOne\Model\ResourceModel\LogRecord
     */
    private $logResource;

    /**
     * @var \Qliro\QliroOne\Model\Logger\LinkRepositoryInterface
     */
    private $linkRepository;

    /**
     * Inject dependencies
     *
     * @param LoggerInterface $psrLogger
     * @param \Qliro\QliroOne\Model\ResourceModel\LogRecord $logResource
     * @param \Qliro\QliroOne\Model\Logger\LinkRepositoryInterface $linkRepository
     */
    public function __construct(
        LoggerInterface $psrLogger,
        LogRecord $logResource,
        LinkRepositoryInterface $linkRepository
    ) {
        $this->psrLogger = $psrLogger;
        $this->logResource = $logResource;
        $this->linkRepository = $linkRepository;
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function emergency($message, array $context = [])
    {
        $this->psrLogger->emergency($message, $this->prepareContext($context));
    }

    /**
     * Action must be taken immediately.
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function alert($message, array $context = [])
    {
        $this->psrLogger->alert($message, $this->prepareContext($context));
    }

    /**
     * Critical conditions.
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function critical($message, array $context = [])
    {
        $this->psrLogger->critical($message, $this->prepareContext($context));
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error($message, array $context = [])
    {
        $this->psrLogger->error($message, $this->prepareContext($context));
    }

    /**
     * Exceptional occurrences that are not errors.
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning($message, array $context = [])
    {
        $this->psrLogger->warning($message, $this->prepareContext($context));
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function notice($message, array $context = [])
    {
        $this->psrLogger->notice($message, $this->prepareContext($context));
    }

    /**
     * Interesting events.
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info($message, array $context = [])
    {
        $this->psrLogger->info($message, $this->prepareContext($context));
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function debug($message, array $context = [])
    {
        $this->psrLogger->debug($message, $this->prepareContext($context));
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $this->psrLogger->log($level, $message, $this->prepareContext($context));
    }

    /**
     * As soon as possible place a tag in the logger to ensure that all log lines can be linked to a checkout session.
     * The tag should be the merchant reference.
     * We will back-patch the log with the new mercant reference
     *
     * @param string $value
     * @return $this
     */
    public function setMerchantReference($value)
    {
        $this->merchantReference = $value;
        if (!empty($value)) {
            $this->logResource->patchMerchantReference($value);
        }

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function setMerchantReferenceFromQuote($quote)
    {
        if ($quote) {
            try {
                $quoteId = $quote->getEntityId();
                $link = $this->linkRepository->getByQuoteId($quoteId);
                $this->setMerchantReference($link->getReference());
            } catch (\Exception $exception) {
                // Do nothing
            }
        }

        return $this;
    }

    /**
     * Add a tag to any futher logging context
     *
     * @param string $tag
     */
    public function addTag($tag)
    {
        $this->tags[] = $tag;
        $this->tags = array_unique($this->tags);
    }

    /**
     * remove a tag from any futher logging context
     *
     * @param string $tag
     */
    public function removeTag($tag)
    {
        $this->tags = array_diff($this->tags, [$tag]);
    }

    /**
     * Clear all tags from any further logging context
     */
    public function clearTags()
    {
        $this->tags = [];
    }

    /**
     * Set message mark.
     * In fact, by setting a mark it pushes previously set mark to the stack.
     * When mark is then set to null, the previously set mark is restored from the stack.
     * It allows to set marks in the folded functions, allowing to restore logger context when exiting the function.
     *
     * @param string $mark
     */
    public function setMark($mark)
    {
        if ($mark) {
            array_unshift($this->marks, $mark);
        } else {
            array_shift($this->marks);
        }
    }

    /**
     * @param int $levels
     * @return string
     */
    public function getStack($levels = 5)
    {
        $exception = new \Exception;
        $stack = '';
        $skip = strpos($exception->getFile(), 'module-qliroone/') + 16;
        foreach (array_slice($exception->getTrace(), 1, $levels) as $one) {
            $stack .= sprintf('|%s:%s', substr($one['file'], $skip), $one['line']);
        }

        return substr($stack, 1);
    }
    /**
     * @param array $context
     * @return array
     */
    private function prepareContext($context)
    {
        if (!empty($this->merchantReference)) {
            $context['reference'] = $this->merchantReference;
        }

        $contextTags = $this->unpackTags($context['tags'] ?? '');
        $context['tags'] = $this->packTags(array_unique(array_merge($contextTags, $this->tags)));

        if (!empty($this->marks)) {
            $context['mark'] = $this->marks[0];
        }

        $context['process_id'] = \getmypid();

        return $context;
    }

    /**
     * @param array $tagsData
     * @return string
     */
    private function packTags($tagsData)
    {
        if (is_string($tagsData)) {
            $tagsData = $this->unpackTags($tagsData);
        }

        return is_array($tagsData) && !empty($tagsData)
            ? trim(implode(',', array_map('trim', (array)$tagsData)), ',')
            : '';
    }

    /**
     * @param string $tagsString
     * @return array
     */
    private function unpackTags($tagsString)
    {
        return $tagsString ? explode(',', $tagsString) : [];
    }
}
