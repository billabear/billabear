<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Command\Integration\Accounting;

use BillaBear\Entity\Refund;
use BillaBear\Integrations\Accounting\Messenger\SyncRefund;
use BillaBear\Repository\RefundRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'billabear:integration:accounting:refund-sync',
    description: 'Sync refunds with accounting system'
)]
class RefundSyncCommand extends Command
{
    public function __construct(
        private RefundRepositoryInterface $refundRepository,
        private SettingsRepositoryInterface $settingsRepository,
        private MessageBusInterface $messageBus,
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Syncing refunds to accounting system');
        $settings = $this->settingsRepository->getDefaultSettings();

        if (!$settings->getAccountingIntegration()->getEnabled()) {
            $output->writeln('No accounting integration set up');

            return Command::FAILURE;
        }
        $max = $this->refundRepository->getTotalCount();
        $progressBar = new ProgressBar($output, $max);
        $lastId = null;
        do {
            $resultList = $this->refundRepository->getList([], lastId: $lastId);
            $lastId = $resultList->getLastKey();

            /** @var Refund $refund */
            foreach ($resultList->getResults() as $refund) {
                $this->messageBus->dispatch(new SyncRefund((string) $refund->getId()));
                $progressBar->advance();
            }
        } while ($resultList->hasMore());

        return Command::SUCCESS;
    }
}
