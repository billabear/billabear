<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Command\Integration\Accounting;

use BillaBear\Background\Invoice\CheckIfPaid;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'billabear:integration:accounting:check-if-invoice-paid',
    description: 'Check if invoice is paid',
)]
class CheckIfInvoicePaidCommand extends Command
{
    public function __construct(private CheckIfPaid $checkIfPaid)
    {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Checking if invoices are paid');
        $this->checkIfPaid->execute();

        return Command::SUCCESS;
    }
}
