<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Api\Data;

/**
 * Log record data interface
 *
 * @api
 */
interface LogRecordInterface
{
    const FIELD_ID = 'id';
    const FIELD_DATE = 'date';
    const FIELD_PROCESS_ID = 'process_id';
    const FIELD_REFERENCE = 'reference';
    const FIELD_TAGS = 'tags';
    const FIELD_MESSAGE = 'message';
    const FIELD_EXTRA = 'extra';
    const FIELD_LEVEL = 'level';

    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getDate();

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return string
     */
    public function getLevel();

    /**
     * @return string
     */
    public function getProcessId();

    /**
     * @return string
     */
    public function getTag();

    /**
     * @return string
     */
    public function getExtra();

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * @param string $date
     * @return $this
     */
    public function setDate($date);

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message);

    /**
     * @param string $value
     * @return $this
     */
    public function setLevel($value);

    /**
     * @param string $process_id
     * @return $this
     */
    public function setProcessId($process_id);

    /**
     * @param string $tag
     * @return $this
     */
    public function setTag($tag);

    /**
     * @param string $extra
     * @return $this
     */
    public function setExtra($extra);
}
