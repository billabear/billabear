<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Command;

use BillaBear\Stats\CreateSubscriptionCountStats;
use BillaBear\Stats\CustomerCreationStats;
use BillaBear\Stats\RevenueEstimatesGeneration;
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
        private RevenueEstimatesGeneration $estimatesGeneration,
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting stats fix command');
        $this->createSubscriptionCountStats->generate();
        $this->customerCreationStats->generate();
        $this->estimatesGeneration->generate();

        return Command::SUCCESS;
    }
}
