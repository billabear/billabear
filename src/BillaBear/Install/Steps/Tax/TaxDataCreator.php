<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Install\Steps\Tax;

use BillaBear\Entity\Country;
use BillaBear\Entity\CountryTaxRule;
use BillaBear\Entity\State;
use BillaBear\Entity\StateTaxRule;
use BillaBear\Entity\TaxType;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Repository\CountryTaxRuleRepositoryInterface;
use BillaBear\Repository\StateRepositoryInterface;
use BillaBear\Repository\StateTaxRuleRepositoryInterface;
use BillaBear\Repository\TaxTypeRepositoryInterface;
use BillaBear\Tax\ThresholdType;
use Parthenon\Common\Exception\NoEntityFoundException;

class TaxDataCreator
{
    public function __construct(
        private DataProvider $countryList,
        private CountryRepositoryInterface $countryRepository,
        private CountryTaxRuleRepositoryInterface $countryTaxRuleRepository,
        private TaxTypeRepositoryInterface $taxTypeRepository,
        private StateRepositoryInterface $stateRepository,
        private StateTaxRuleRepositoryInterface $stateTaxRuleRepository,
    ) {
    }

    public function process(): void
    {
        $hasDefaultTaxRate = false;
        foreach ($this->countryList->getCountryList() as $countryData) {
            $country = new Country();
            $country->setName($countryData['name']);
            $country->setIsoCode($countryData['code']);
            $country->setCurrency($countryData['currency']);
            $country->setThreshold($countryData['threshold']);
            $country->setTransactionThreshold($countryData['transaction_threshold'] ?? null);
            $country->setThresholdType(ThresholdType::from($countryData['threshold_type'] ?? 'rolling'));
            $country->setInEu($countryData['in_eu']);
            $country->setEnabled(true);
            $country->setCreatedAt(new \DateTime());
            $country->setCollecting(false);

            $this->countryRepository->save($country);

            $rates = $countryData['rates'] ?? [];
            foreach ($rates as $name => $data) {
                try {
                    $taxType = $this->taxTypeRepository->getByName($name);
                } catch (NoEntityFoundException) {
                    $taxType = new TaxType();
                    $taxType->setName($name);
                    // Note the !$
                    $taxType->setDefault(!$hasDefaultTaxRate);
                    $hasDefaultTaxRate = true;
                    $this->taxTypeRepository->save($taxType);
                }

                $taxRule = new CountryTaxRule();
                $taxRule->setTaxType($taxType);
                $taxRule->setTaxRate($data['rate']);
                $taxRule->setIsDefault($data['default']);
                $taxRule->setCreatedAt(new \DateTime());
                $taxRule->setCountry($country);
                $taxRule->setValidFrom(new \DateTime());

                $this->countryTaxRuleRepository->save($taxRule);
            }

            $states = $countryData['states'] ?? [];
            foreach ($states as $code => $data) {
                $state = new State();
                $state->setCountry($country);
                $state->setName($data['name']);
                $state->setCode($code);
                $state->setThreshold($data['threshold']);
                $state->setTransactionThreshold($data['transaction_threshold'] ?? null);
                $state->setThresholdType(ThresholdType::from($data['threshold_type'] ?? 'calendar'));
                $state->setCollecting(false);

                $this->stateRepository->save($state);

                $rates = $data['rates'] ?? [];
                foreach ($rates as $name => $rate) {
                    try {
                        $taxType = $this->taxTypeRepository->getByName($name);
                    } catch (NoEntityFoundException) {
                        $taxType = new TaxType();
                        $taxType->setName($name);
                        $taxType->setDefault(true);

                        $this->taxTypeRepository->save($taxType);
                    }

                    $stateTaxRule = new StateTaxRule();
                    $stateTaxRule->setState($state);
                    $stateTaxRule->setTaxType($taxType);
                    $stateTaxRule->setTaxRate($rate['rate']);
                    $stateTaxRule->setIsDefault($rate['default']);
                    $stateTaxRule->setCreatedAt(new \DateTime());
                    $stateTaxRule->setValidFrom(new \DateTime());

                    $this->stateTaxRuleRepository->save($stateTaxRule);
                }
            }
        }
    }
}
