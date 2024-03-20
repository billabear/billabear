<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Command;

use App\Entity\StripeImport;
use App\Import\Stripe\PriceImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'billabear:stripe:import-price', description: 'Import price data from stripe')]
class StripePriceImportCommand extends Command
{
    public function __construct(private PriceImporter $priceImporter)
    {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start stripe price import command');
        $import = new StripeImport();
        $this->priceImporter->import($import, false);

        return Command::SUCCESS;
    }
}
