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

use App\Entity\StripeImport;
use App\Import\Stripe\SubscriptionImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'billabear:stripe:import-subscription', description: 'Import subscription data from stripe')]
class StripeSubscriptionImportCommand extends Command
{
    public function __construct(private SubscriptionImporter $subscriptionImporter)
    {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Start stripe subscription import command');
        $import = new StripeImport();
        $this->subscriptionImporter->import($import, false);

        return Command::SUCCESS;
    }
}
