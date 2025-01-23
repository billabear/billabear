<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Command;

use BillaBear\Entity\StripeImport;
use BillaBear\Import\Stripe\ChargeBackImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'billabear:stripe:import-charge-back', description: 'Import charge back data from stripe')]
class StripeChargeBackImportCommand extends Command
{
    public function __construct(private ChargeBackImporter $chargeBackImporter)
    {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start stripe charge back import command');
        $import = new StripeImport();
        $this->chargeBackImporter->import($import, false);

        return Command::SUCCESS;
    }
}
