<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Command;

use App\Background\ExpiringCards\DayBefore;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'billabear:expiring-cards:day-before', description: 'Handle the day before next charge check')]
class ExpiringCardsDayBeforeCommand extends Command
{
    public function __construct(private DayBefore $dayBefore)
    {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start checking the expiring cards if they are to be charge within the next 24 hours');
        $this->dayBefore->execute();

        return Command::SUCCESS;
    }
}
