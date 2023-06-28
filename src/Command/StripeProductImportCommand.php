<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Command;

use App\Entity\StripeImport;
use App\Import\Stripe\ProductImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'billabear:stripe:import-product', description: 'Import product data from stripe')]
class StripeProductImportCommand extends Command
{
    public function __construct(private ProductImporter $productImporter)
    {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Start stripe product import command');
        $import = new StripeImport();
        $this->productImporter->import($import, false);

        return Command::SUCCESS;
    }
}
