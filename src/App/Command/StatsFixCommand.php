<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Command;

use App\Stats\CreateSubscriptionCountStats;
use App\Stats\CustomerCreationStats;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('billabear:stats:fix', description: 'A command to fix stats by regenerating them.')]
class StatsFixCommand extends Command
{
    public function __construct(
        private CreateSubscriptionCountStats $createSubscriptionCountStats,
        private CustomerCreationStats $customerCreationStats,
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting stats fix command');
        $this->createSubscriptionCountStats->generate();
        $this->customerCreationStats->generate();

        return Command::SUCCESS;
    }
}
