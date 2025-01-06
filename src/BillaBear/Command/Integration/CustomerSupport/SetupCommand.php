<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Command\Integration\CustomerSupport;

use BillaBear\Integrations\CustomerSupport\Action\Setup;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'billabear:integration:customer-support:setup',
    description: 'Setup the customer support integration',
)]
class SetupCommand extends Command
{
    public function __construct(
        private Setup $setup,
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Setting up customer support integration');
        $this->setup->setup();

        return Command::SUCCESS;
    }
}
