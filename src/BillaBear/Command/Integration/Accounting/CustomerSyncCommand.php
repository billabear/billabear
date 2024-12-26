<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Command\Integration\Accounting;

use BillaBear\Entity\Customer;
use BillaBear\Integrations\Accounting\AccountingIntegrationInterface;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'billabear:integration:accounting:customer-sync',
    description: 'Sync customers to accounting system'
)]
class CustomerSyncCommand extends Command
{
    use LoggerAwareTrait;

    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private SettingsRepositoryInterface $settingsRepository,
        private IntegrationManager $integrationManager,
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Syncing customers to accounting system');
        $settings = $this->settingsRepository->getDefaultSettings();

        if (!$settings->getAccountingIntegration()->getEnabled()) {
            $output->writeln('No accounting integration set up');

            return Command::FAILURE;
        }

        /** @var AccountingIntegrationInterface $integration */
        $integration = $this->integrationManager->getIntegration($settings->getAccountingIntegration()->getIntegration());

        $lastId = null;
        do {
            $output->writeln('Syncing batch of customers to accounting system');
            $resultList = $this->customerRepository->getList([], lastId: $lastId);
            $lastId = $resultList->getLastKey();

            /** @var Customer $customer */
            foreach ($resultList->getResults() as $customer) {
                if ($customer->getAccountingReference()) {
                    $integration->getCustomerService()->update($customer);
                } else {
                    $registration = $integration->getCustomerService()->register($customer);
                    $customer->setAccountingReference($registration->reference);
                }
                $this->customerRepository->save($customer);
            }
        } while ($resultList->hasMore());

        return Command::SUCCESS;
    }
}
