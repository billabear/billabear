<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Command;

use BillaBear\Background\Invoice\GenerateNewInvoices;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'billabear:invoices:generate-new', description: 'Start the process of generating new invoices')]
class InvoicesGenerateNewCommand extends Command
{
    public function __construct(private GenerateNewInvoices $generateNewInvoices)
    {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start expiring commands generating new invoices');
        $this->generateNewInvoices->execute();

        return Command::SUCCESS;
    }
}
