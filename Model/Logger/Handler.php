<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

// @codingStandardsIgnoreFile
// phpcs:ignoreFile

namespace Qliro\QliroOne\Model\Logger;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Qliro\QliroOne\Model\Config;
use Monolog\Formatter\FormatterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ResourceConnection\ConnectionFactory;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Qliro\QliroOne\Model\ResourceModel\LogRecord;
use Qliro\QliroOne\Api\Data\LogRecordInterface;

/**
 * Logger DB handler class
 */
class Handler extends AbstractProcessingHandler
{
    /**
     * @var \Magento\Payment\Model\Method\Adapter
     */
    private $config;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|null
     */
    private $connection = null;

    /**
     * @var \Magento\Framework\App\ResourceConnection\ConnectionFactory
     */
    private $connectionFactory;

    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * Handler constructor.
     *
     * @param FormatterInterface $formatter
     * @param Config $config
     * @param ConnectionFactory $connectionFactory
     * @param DeploymentConfig $deploymentConfig
     */
    public function __construct(
        FormatterInterface $formatter,
        Config $config,
        ConnectionFactory $connectionFactory,
        DeploymentConfig $deploymentConfig
    ) {
        $this->formatter = $formatter;
        $this->config = $config;
        $this->connectionFactory = $connectionFactory;
        $this->deploymentConfig = $deploymentConfig;

        parent::__construct();
    }

    /**
     * Make the level be dynamically aware of the configured log level
     *
     * @param array $record
     * @return bool
     */
    public function isHandling(array $record)
    {
        return $record['level'] >= $this->getLevel();

    }

    /**
     * Make the level be dynamically aware of the configured log level
     *
     * @return int
     */
    public function getLevel()
    {
        $this->level = Logger::toMonologLevel($this->config->getLoggingLevel());

        return $this->level;
    }

    /**
     * @param array $record
     * @throws \DomainException
     */
    protected function write(array $record)
    {
        $context = $record['context'];
        $record = $record['formatted'];

        $mark = $context['mark'] ?? null;
        $message = ($mark ? sprintf('%s: ', strtoupper($mark)) : null) . $record['message'];

        $connection = $this->getConnection();
        $connection->insert(
            $connection->getTableName(LogRecord::TABLE_LOG),
            [
                LogRecordInterface::FIELD_DATE => $record['datetime'],
                LogRecordInterface::FIELD_LEVEL => $record['level_name'],
                LogRecordInterface::FIELD_MESSAGE => $message,
                LogRecordInterface::FIELD_REFERENCE => $context['reference'] ?? '',
                LogRecordInterface::FIELD_TAGS => $context['tags'] ?? '',
                LogRecordInterface::FIELD_PROCESS_ID => $context['process_id'] ?? '',
                LogRecordInterface::FIELD_EXTRA => $this->encodeExtra($context['extra'] ?? ''),
            ]
        );
    }

    /**
     * Get a log DB connection that uses same config as default connection, but is separate
     *
     * @return AdapterInterface
     * @throws \DomainException
     */
    private function getConnection()
    {
        if (!$this->connection) {
            $connectionName = ResourceConnection::DEFAULT_CONNECTION;

            $connectionConfig = $this->deploymentConfig->get(
                ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTIONS . '/' . $connectionName
            );

            if ($connectionConfig) {
                $this->connection = $this->connectionFactory->create($connectionConfig);
            } else {
                throw new \DomainException("Connection '$connectionName' is not defined");
            }

        }

        return $this->connection;
    }

    /**
     * @param array|string $data
     * @return string
     */
    private function encodeExtra($data)
    {
        try {
            $serializedData = is_array($data) ? $this->serialize($data) : $data;
        } catch (\Exception $exception) {
            $serializedData = null;
        }

        return $serializedData;
    }

    /**
     * Serialize JSON using pretty print and some other options
     *
     * @param array $data
     * @return false|string
     */
    private function serialize($data)
    {
        return \json_encode($data, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
    }
}
