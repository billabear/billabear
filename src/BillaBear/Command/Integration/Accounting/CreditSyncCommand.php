<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Command\Integration\Accounting;

use BillaBear\Entity\Credit;
use BillaBear\Integrations\Accounting\Messenger\SyncCredit;
use BillaBear\Repository\CreditRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'billabear:integration:accounting:credit-sync',
    description: 'Sync credit with accounting system'
)]
class CreditSyncCommand extends Command
{
    public function __construct(
        private CreditRepositoryInterface $creditRepository,
        private SettingsRepositoryInterface $settingsRepository,
        private MessageBusInterface $messageBus,
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Syncing credit to accounting system');
        $settings = $this->settingsRepository->getDefaultSettings();

        if (!$settings->getAccountingIntegration()->getEnabled()) {
            $output->writeln('No accounting integration set up');

            return Command::FAILURE;
        }
        $max = $this->creditRepository->getTotalCount();
        $progressBar = new ProgressBar($output, $max);
        $lastId = null;
        do {
            $resultList = $this->creditRepository->getList([], lastId: $lastId);
            $lastId = $resultList->getLastKey();

            /** @var Credit $credit */
            foreach ($resultList->getResults() as $credit) {
                $this->messageBus->dispatch(new SyncCredit((string) $credit->getId()));
                $progressBar->advance();
            }
        } while ($resultList->hasMore());

        return Command::SUCCESS;
    }
}
