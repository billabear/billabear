<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Command\Integration\Accounting;

use BillaBear\Entity\Invoice;
use BillaBear\Integrations\Accounting\Messenger\SyncInvoice;
use BillaBear\Repository\InvoiceRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'billabear:integration:accounting:invoice-sync',
    description: 'Sync invoices to accounting system'
)]
class InvoiceSyncCommand extends Command
{
    use LoggerAwareTrait;

    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private SettingsRepositoryInterface $settingsRepository,
        private MessageBusInterface $messageBus,
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Syncing invoices to accounting system');
        $settings = $this->settingsRepository->getDefaultSettings();

        if (!$settings->getAccountingIntegration()->getEnabled()) {
            $output->writeln('No accounting integration set up');

            return Command::FAILURE;
        }
        $max = $this->invoiceRepository->getTotalCount();
        $progressBar = new ProgressBar($output, $max);
        $lastId = null;
        do {
            $resultList = $this->invoiceRepository->getList([], lastId: $lastId);
            $lastId = $resultList->getLastKey();

            /** @var Invoice $invoice */
            foreach ($resultList->getResults() as $invoice) {
                $this->messageBus->dispatch(new SyncInvoice((string) $invoice->getId()));
                $progressBar->advance();
            }
        } while ($resultList->hasMore());

        return Command::SUCCESS;
    }
}
