<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Command;

use BillaBear\Install\Upgrade\CountryUpdater;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'billabear:country:data-sync', description: 'Sync country data with data provider. For upgrades only.')]
class CountryDataSyncCommand extends Command
{
    public function __construct(
        private CountryUpdater $countryUpdater,
    ) {
        parent::__construct(null);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting country data sync');

        $this->countryUpdater->execute();

        return Command::SUCCESS;
    }
}
