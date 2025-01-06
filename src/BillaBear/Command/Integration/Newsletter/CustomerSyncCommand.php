<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Command\Integration\Newsletter;

use BillaBear\Entity\Customer;
use BillaBear\Integrations\Newsletter\Messenger\SyncCustomer;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'billabear:integration:newsletter:customer-sync',
    description: 'Sync customers to newsletter system'
)]
class CustomerSyncCommand extends Command
{
    use LoggerAwareTrait;

    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private SettingsRepositoryInterface $settingsRepository,
        private MessageBusInterface $messageBus,
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Syncing customers to newsletter system');
        $settings = $this->settingsRepository->getDefaultSettings();

        if (!$settings->getNewsletterIntegration()->getEnabled()) {
            $output->writeln('No newsletter integration set up');

            return Command::FAILURE;
        }

        $max = $this->customerRepository->getTotalCount();
        $progressBar = new ProgressBar($output, $max);
        $lastId = null;
        do {
            $resultList = $this->customerRepository->getList([], lastId: $lastId);
            $lastId = $resultList->getLastKey();

            /** @var Customer $customer */
            foreach ($resultList->getResults() as $customer) {
                $this->messageBus->dispatch(new SyncCustomer((string) $customer->getId()));
                $progressBar->advance();
            }
        } while ($resultList->hasMore());

        return Command::SUCCESS;
    }
}
