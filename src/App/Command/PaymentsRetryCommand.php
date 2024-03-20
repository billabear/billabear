<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Command;

use App\Background\Payments\RetryPaymentsProcess;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'billabear:payments:retry', description: 'Start the process of retrying payments')]
class PaymentsRetryCommand extends Command
{
    public function __construct(private RetryPaymentsProcess $retryPaymentsProcess)
    {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start retry failed payments command');
        $this->retryPaymentsProcess->execute();

        return Command::SUCCESS;
    }
}
