<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Start checking the expiring cards if they are to be charge within the next 24 hours');
        $this->dayBefore->execute();

        return Command::SUCCESS;
    }
}
