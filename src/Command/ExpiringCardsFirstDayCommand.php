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

use App\Background\ExpiringCards\StartProcess;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'billabear:expiring-cards:first-day', description: 'Start the process of warning customers their cards are going to expire')]
class ExpiringCardsFirstDayCommand extends Command
{
    public function __construct(private StartProcess $startProcess)
    {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Start expiring commands first day command');
        $this->startProcess->execute();

        return Command::SUCCESS;
    }
}