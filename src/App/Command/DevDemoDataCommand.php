<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Command;

use App\Dev\DemoData\CustomerCreation;
use App\Dev\DemoData\InvoiceCreation;
use App\Dev\DemoData\SubscriptionCreation;
use App\Dev\DemoData\SubscriptionPlanCreation;
use App\Stats\CreateSubscriptionCountStats;
use App\Stats\RevenueEstimatesGeneration;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'billabear:dev:demo-data', description: 'Generate some demo data')]
class DevDemoDataCommand extends Command
{
    public const NUMBER_OF_CUSTOMERS = 2000;

    private static int $count;
    private static \DateTime $date;

    public static function getNumberOfCustomers(): int
    {
        return self::$count;
    }

    public static function getStartDate(): \DateTime
    {
        return clone self::$date;
    }

    public function __construct(
        private CustomerCreation $customerCreation,
        private SubscriptionPlanCreation $subscriptionPlanCreation,
        private SubscriptionCreation $subscriptionCreation,
        private InvoiceCreation $invoiceCreation,
        private RevenueEstimatesGeneration $estimatesGeneration,
        private CreateSubscriptionCountStats $createSubscriptionCountStats,
    ) {
        parent::__construct(null);
    }

    protected function configure()
    {
        $this->addOption('date', mode: InputOption::VALUE_REQUIRED, description: 'The starting date to add new customers and subscriptions', default: '-18 months')
            ->addOption('count', mode: InputOption::VALUE_REQUIRED, description: 'The number of users to add', default: 3000)
            ->addOption('products', mode: InputOption::VALUE_OPTIONAL, description: 'If products and features are to be added', default: 'true');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        self::$count = $input->getOption('count');
        self::$date = new \DateTime($input->getOption('date'));
        $products = $input->getOption('products');

        $output->writeln('Start creating demo data');
        $this->customerCreation->createData($output);
        if ('true' === strtolower($products)) {
            $this->subscriptionPlanCreation->createData($output);
        }
        $this->subscriptionCreation->createData($output);
        $this->invoiceCreation->createData($output);
        $this->estimatesGeneration->generate();
        $this->createSubscriptionCountStats->generate();

        return Command::SUCCESS;
    }
}
