<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Command;

use BillaBear\Entity\Country;
use BillaBear\Entity\State;
use BillaBear\Install\Steps\Tax\DataProvider;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Repository\StateRepositoryInterface;
use BillaBear\Repository\TaxTypeRepositoryInterface;
use BillaBear\Tax\ThresholdType;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'billabear:country:data-sync', description: 'Sync country data with data provider. For upgrades only.')]
class CountryDataSyncCommand extends Command
{
    public function __construct(
        private DataProvider $dataProvider,
        private CountryRepositoryInterface $countryRepository,
        private StateRepositoryInterface $stateRepository,
        private TaxTypeRepositoryInterface $taxTypeRepository,
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->dataProvider->getCountryList();

        foreach ($data as $countryCode => $countryData) {
            try {
                $country = $this->countryRepository->getByIsoCode($countryCode);
            } catch (NoEntityFoundException) {
                $country = new Country();
                $country->setIsoCode($countryCode);
                $country->setEnabled(true);
                $country->setCreatedAt(new \DateTime());
                $country->setCollecting(false);
            }

            $country->setName($countryData['name']);
            $country->setCurrency($countryData['currency']);
            $country->setThreshold($countryData['threshold']);
            $country->setInEu($countryData['in_eu']);
            $country->setCurrency($countryData['currency']);
            $country->setTransactionThreshold($countryData['transaction_threshold'] ?? null);
            $country->setThresholdType(ThresholdType::from($countryData['threshold_type'] ?? 'rolling'));

            $this->countryRepository->save($country);

            if (isset($countryData['states'])) {
                foreach ($countryData['states'] as $stateCode => $stateData) {
                    try {
                        $state = $this->stateRepository->getByCode($stateCode);
                    } catch (NoEntityFoundException) {
                        $state = new State();
                        $state->setCode($stateCode);
                        $state->setCollecting(false);
                    }
                    $state->setName($stateData['name']);
                    $state->setCountry($country);
                    $state->setThreshold($stateData['threshold']);
                    $state->setTransactionThreshold($stateData['transaction_threshold'] ?? null);
                    $state->setThresholdType(ThresholdType::from($stateData['threshold_type'] ?? 'rolling'));

                    $this->stateRepository->save($state);
                }
            }
        }

        return Command::SUCCESS;
    }
}
