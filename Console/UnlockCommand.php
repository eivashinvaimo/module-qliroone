<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

// @codingStandardsIgnoreFile
// phpcs:ignoreFile

namespace Qliro\QliroOne\Console;

use Qliro\QliroOne\Model\ResourceModel\Lock;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UnlockCommand
 * Test apis.
 */
class UnlockCommand extends AbstractCommand
{
    const COMMAND_RUN = 'qliroone:api:unlock';

    /**
     * @var string
     */
    protected $orderId;

    /**
     * @var boolean
     */
    protected $force;

    /**
     * Configure the CLI command
     */
    protected function configure()
    {
        parent::configure();

        $this->setName(self::COMMAND_RUN);
        $this->setDescription('Unlock order creation for order');
        $this->addArgument('orderid', InputArgument::REQUIRED, 'Qliro order id', null);
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'ignore process id when unlocking');
    }

    /**
     * Initialize the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->orderId = $input->getArgument('orderid');
        $this->force = $input->getOption('force');
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Unlock Qliro order</comment>');

        /** @var Lock */
        $lockManager = $this->getObjectManager()->get(Lock::class);

        try {
            $result = $lockManager->unlock($this->orderId, $this->force);
            $output->writeln(sprintf('<info>%s Unlock of Qliro Order</info>', $result ? 'Successful' : 'Unsuccessful'));
        } catch (\Qliro\QliroOne\Model\Api\Client\Exception\ClientException $exception) {
            /** @var \GuzzleHttp\Exception\RequestException $origException */
            $origException = $exception->getPrevious();

            fprintf(STDOUT, 'Merchant API exception:');

            fprintf(
                STDOUT,
                \json_encode(
                    [
                        'request.uri' => $origException->getRequest()->getUri(),
                        'request.method' => $origException->getRequest()->getMethod(),
                        'request.headers' => $origException->getRequest()->getHeaders(),
                        'request.body' => $origException->getRequest()->getBody()->getContents(),
                        'response.status' => $origException->getResponse()->getStatusCode(),
                        'response.headers' => $origException->getResponse()->getHeaders(),
                        'response.body' => $origException->getResponse()->getBody()->getContents(),
                    ],
                    JSON_PRETTY_PRINT
                )
            );
        }

        return 0;
    }
}
