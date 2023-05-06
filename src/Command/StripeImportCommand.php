<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Command;

use App\Entity\StripeImport;
use App\Import\Stripe\ChargeBackImporter;
use App\Import\Stripe\CustomerImporter;
use App\Import\Stripe\PaymentImporter;
use App\Import\Stripe\PriceImporter;
use App\Import\Stripe\ProductImporter;
use App\Import\Stripe\RefundImporter;
use App\Import\Stripe\SubscriptionImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'billabear:stripe:import', description: 'Import all data from stripe')]
class StripeImportCommand extends Command
{
    public function __construct(
        private CustomerImporter $customerImporter,
        private ProductImporter $productImporter,
        private PriceImporter $priceImporter,
        private SubscriptionImporter $subscriptionImporter,
        private PaymentImporter $paymentImporter,
        private RefundImporter $refundImporter,
        private ChargeBackImporter $chargeBackImporter,
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Start stripe import command');
        $import = new StripeImport();
        $this->customerImporter->import($import, false);
        $output->writeln('Start stripe product import command');
        $this->productImporter->import($import, false);
        $output->writeln('Start stripe price import command');
        $this->priceImporter->import($import, false);
        $output->writeln('Start stripe subscription import command');
        $this->subscriptionImporter->import($import, false);
        $output->writeln('Start stripe payment import command');
        $this->paymentImporter->import($import, false);
        $output->writeln('Start stripe refund import command');
        $this->refundImporter->import($import, false);
        $output->writeln('Start stripe charge back import command');
        $this->chargeBackImporter->import($import, false);

        return Command::SUCCESS;
    }
}
