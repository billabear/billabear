<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 */

namespace App\Command;

use App\Entity\StripeImport;
use App\Import\Stripe\PaymentImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'billabear:stripe:import-payments', description: 'Import payment data from stripe')]
class StripePaymentImportCommand extends Command
{
    public function __construct(private PaymentImporter $paymentImporter)
    {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start stripe payment import command');
        $import = new StripeImport();
        $this->paymentImporter->import($import, false);

        return Command::SUCCESS;
    }
}
