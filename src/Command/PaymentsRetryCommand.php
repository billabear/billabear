<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Start retry failed payments command');
        $this->retryPaymentsProcess->execute();

        return Command::SUCCESS;
    }
}
