<?php
/**
 * Copyright © Qliro AB. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Qliro\QliroOne\Console;

use Qliro\QliroOne\Api\Data\QliroOrderCreateRequestInterface;
use Qliro\QliroOne\Model\ContainerMapper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Qliro\QliroOne\Model\Api\Service;

/**
 * Class TestCommand
 * Test apis.
 */
class TestCommand extends AbstractCommand
{
    const COMMAND_RUN = 'qliroone:api:test';

    /**
     * Configure the CLI command
     */
    protected function configure()
    {
        parent::configure();

        $this->setName(self::COMMAND_RUN);
        $this->setDescription('Verify QliroOne API');
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
        $output->writeln('<comment>Test</comment>');

        /** @var Service $service */
        $service = $this->getObjectManager()->get(Service::class);

        /** @var \Qliro\QliroOne\Model\ContainerMapper $containerMapper */
        $containerMapper = $this->getObjectManager()->get(ContainerMapper::class);

        /** @var \Qliro\QliroOne\Api\Data\QliroOrderCreateRequestInterface $createRequest */
        $createRequest = $this->getObjectManager()->get(QliroOrderCreateRequestInterface::class);

        $payload = [
            "MerchantReference" => "211300540",
            "Currency" => "SEK",
            "Country" => "SE",
            "Language" => "sv-se",
            "MerchantConfirmationUrl" => "http://baw.ddns.net:8080/qliroone/htdocs/sv/checkout/qliro/saveOrder?XDEBUG_SESSION_START=PHPSTORM",
            "MerchantTermsUrl" => "http://baw.ddns.net:8080/qliroone/htdocs/sv/terms",
            "MerchantOrderValidationUrl" => "http://baw.ddns.net:8080/qliroone/htdocs/sv/checkout/qliro/validate?XDEBUG_SESSION_START=PHPSTORM",
            "MerchantOrderAvailableShippingMethodsUrl" => "http://baw.ddns.net:8080/qliroone/htdocs/sv/checkout/qliro/shipping?XDEBUG_SESSION_START=PHPSTORM",
            "MerchantCheckoutStatusPushUrl" => "http://baw.ddns.net:8080/qliroone/htdocs/qliroapi/order/index/order_id/211300540/token/NmIwMTNmY2Q0YzYwOWE2ZjQ3MzVkMDcyNDMzNTg1ZjMwZDkyMmI1NDhlNDFhN2Q1YWJiZWI1MmVhZWNiYWQwYQ==/",
            "MerchantOrderManagementStatusPushUrl" => "http://baw.ddns.net:8080/qliroone/htdocs/sv/qliroapi/notification?XDEBUG_SESSION_START=PHPSTORM",
            "PrimaryColor" => "#000000",
            "CallToActionColor" => "#0000FF",
            "BackgroundColor" => "#FFFFFF",
            "AskForNewsletterSignup" => true,
            "OrderItems" => [
                [
                    "MerchantReference" => "S001",
                    "Description" => "Test product - Simple",
                    "Type" => "Product",
                    "Quantity" => 1,
                    "PricePerItemIncVat" => "100.00",
                    "PricePerItemExVat" => "80.00"
                ]
            ],
        ];

        $containerMapper->fromArray($payload, $createRequest);

        $a = 1;

        try {
            $response = $service->post('checkout/merchantapi/orders', $payload);

            print_r([
                'headers' => $service->getResponseHeaders(),
                'response' => $response,
                'status_code' => $service->getResponseStatusCode(),
                'reason' => $service->getResponseReason(),
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $exception) {
            print_r([
                'request.uri' => $exception->getRequest()->getUri(),
                'request.method' => $exception->getRequest()->getMethod(),
                'request.headers' => $exception->getRequest()->getHeaders(),
                'request.body' => $exception->getRequest()->getBody()->getContents(),
                'response.status' => $exception->getResponse()->getStatusCode(),
                'response.headers' => $exception->getResponse()->getHeaders(),
                'response.body' => $exception->getResponse()->getBody()->getContents(),
            ]);
        }

        //$response = $service->get('checkout/merchantapi/orders/', ['merchantReference' => '211300540']);
        //if (!is_string($response)) {
        //    $response = var_export($response, true);
        //}
        //$output->writeln("<info>$response</info>");

        return 0;
    }
}
