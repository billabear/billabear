<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting stats fix command');
        $this->createSubscriptionCountStats->generate();
        $this->customerCreationStats->generate();

        return Command::SUCCESS;
    }
}
