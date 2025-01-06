<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Command\Upgrade;

use BillaBear\Install\Steps\DataStep;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'billabear:upgrade:20240101', description: 'Handles adding data that is needed after the upgrade')]
class Upgrade20240101 extends Command
{
    public function __construct(private DataStep $dataStep)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting the process to add data for upgrade');
        $this->dataStep->install();

        return Command::SUCCESS;
    }
}
