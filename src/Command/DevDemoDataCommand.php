<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Command;

use App\Dev\DemoData\CustomerCreation;
use App\Dev\DemoData\InvoiceCreation;
use App\Dev\DemoData\SubscriptionCreation;
use App\Dev\DemoData\SubscriptionPlanCreation;
use App\Stats\RevenueEstimatesGeneration;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'billabear:dev:demo-data', description: 'Generate some demo data')]
class DevDemoDataCommand extends Command
{
    public const NUMBER_OF_CUSTOMERS = 2000;

    public function __construct(
        private CustomerCreation $customerCreation,
        private SubscriptionPlanCreation $subscriptionPlanCreation,
        private SubscriptionCreation $subscriptionCreation,
        private InvoiceCreation $invoiceCreation,
        private RevenueEstimatesGeneration $estimatesGeneration,
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Start creating demo data');
        $this->customerCreation->createData($output);
        $this->subscriptionPlanCreation->createData($output);
        $this->subscriptionCreation->createData($output);
        $this->invoiceCreation->createData($output);
        $this->estimatesGeneration->generate();

        return Command::SUCCESS;
    }
}
