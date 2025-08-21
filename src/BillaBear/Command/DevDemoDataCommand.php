<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Command;

use BillaBear\Background\Payments\ExchangeRatesFetchProcess;
use BillaBear\Dev\DemoData\CustomerCreation;
use BillaBear\Dev\DemoData\InvoiceCreation;
use BillaBear\Dev\DemoData\SubscriptionCreation;
use BillaBear\Dev\DemoData\SubscriptionPlanCreation;
use BillaBear\Stats\CreateSubscriptionCountStats;
use BillaBear\Stats\RevenueEstimatesGeneration;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'billabear:dev:demo-data', description: 'Generate some demo data')]
class DevDemoDataCommand extends Command
{
    private static int $count;
    private static \DateTime $date;

    public function __construct(
        private readonly CustomerCreation $customerCreation,
        private readonly SubscriptionPlanCreation $subscriptionPlanCreation,
        private readonly SubscriptionCreation $subscriptionCreation,
        private readonly InvoiceCreation $invoiceCreation,
        private readonly RevenueEstimatesGeneration $estimatesGeneration,
        private readonly CreateSubscriptionCountStats $createSubscriptionCountStats,
        private readonly ExchangeRatesFetchProcess $exchangeRatesFetchProcess,
    ) {
        parent::__construct();
    }

    public static function getNumberOfCustomers(): int
    {
        return self::$count;
    }

    public static function getStartDate(): \DateTime
    {
        return clone self::$date;
    }

    protected function configure(): void
    {
        $this->addOption('date', mode: InputOption::VALUE_REQUIRED, description: 'The starting date to add new customers and subscriptions', default: '-18 months')
            ->addOption('count', mode: InputOption::VALUE_REQUIRED, description: 'The number of users to add', default: 3000)
            ->addOption('products', mode: InputOption::VALUE_OPTIONAL, description: 'If products and features are to be added', default: 'true')
            ->addOption('stripe', mode: InputOption::VALUE_OPTIONAL, description: 'If data needs to be added to Stripe. (Takes longer)', default: 'true');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        self::$count = $input->getOption('count');
        $date = new \DateTime($input->getOption('date'));
        static::$date = $date;
        $products = $input->getOption('products');
        $writeToStripe = 'true' === strtolower($input->getOption('stripe'));

        $output->writeln('Start creating demo data');
        $this->customerCreation->createData($output, $writeToStripe);
        if ('true' === strtolower($products)) {
            $this->subscriptionPlanCreation->createData($output, $writeToStripe);
        }
        $output->writeln('Fetching exchange rates');
        $this->exchangeRatesFetchProcess->process();
        $this->subscriptionCreation->createData($output, $writeToStripe);
        $this->invoiceCreation->createData($output, $writeToStripe);
        $output->writeln('Generating data');
        $this->estimatesGeneration->generate();
        $this->createSubscriptionCountStats->generate();

        return Command::SUCCESS;
    }
}
