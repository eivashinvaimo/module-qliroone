<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

// @codingStandardsIgnoreFile
// phpcs:ignoreFile

namespace Qliro\QliroOne\Console;

use Qliro\QliroOne\Model\ContainerMapper;
use Qliro\QliroOne\Model\Management;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Qliro\QliroOne\Model\Api\Client\Merchant;

/**
 * Class GetOrderCommand
 * Test apis.
 */
class GetOrderCommand extends AbstractCommand
{
    const COMMAND_RUN = 'qliroone:api:getorder';

    /**
     * @var string
     */
    private $orderId;

    /**
     * @var boolean
     */
    private $createMagentoOrder;

    /**
    /**
     * Configure the CLI command
     */
    protected function configure()
    {
        parent::configure();

        $this->setName(self::COMMAND_RUN);
        $this->setDescription('Get Order from Qliro');
        $this->addArgument('orderid', InputArgument::REQUIRED, 'Existing Qliro order id', null);
        $this->addArgument('create', InputArgument::OPTIONAL, 'Create Magento order from Qliro order', false);
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
        $this->createMagentoOrder = $input->getArgument('create');
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
        $output->writeln('<comment>Getting QliroOne order</comment>');

        /** @var Merchant $merchant */
        $merchant = $this->getObjectManager()->get(Merchant::class);

        /** @var \Qliro\QliroOne\Model\ContainerMapper $containerMapper */
        $containerMapper = $this->getObjectManager()->get(ContainerMapper::class);

        try {
            $responseContainer = $merchant->getOrder($this->orderId);
            $responsePayload = $containerMapper->toArray($responseContainer);

            fprintf(STDOUT, \json_encode($responsePayload, JSON_PRETTY_PRINT));

            if ($this->createMagentoOrder) {
                /** @var \Qliro\QliroOne\Model\Management $management */
                $management = $this->getObjectManager()->get(Management::class);

                $management->placeOrder($responseContainer);
                $output->writeln('<comment>Place order successfull</comment>');
            }
        } catch (\Qliro\QliroOne\Model\Exception\TerminalException $exception) {
            /** @var \GuzzleHttp\Exception\RequestException $origException */
            $origException = $exception->getPrevious();

            fprintf(STDOUT, 'Merchant API exception:');
            fprintf(STDOUT, $origException->getMessage());

        } catch (\Qliro\QliroOne\Model\Exception\FailToLockException $exception) {
            fprintf(STDOUT, 'Locking:');
            fprintf(STDOUT, $exception->getMessage());

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
