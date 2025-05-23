<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Command;

use BillaBear\Background\Invoice\DisableOverdueInvoices;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'billabear:invoices:disable-overdue', description: 'Start the process of disabling overdue invoice')]
class InvoiceDisableOverdueCommand extends Command
{
    public function __construct(private DisableOverdueInvoices $disableOverdueInvoices)
    {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start disabling overdue customers');
        $this->disableOverdueInvoices->execute();

        return Command::SUCCESS;
    }
}
