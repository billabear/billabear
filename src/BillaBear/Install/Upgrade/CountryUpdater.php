<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Install\Upgrade;

use BillaBear\Entity\Country;
use BillaBear\Entity\CountryTaxRule;
use BillaBear\Entity\State;
use BillaBear\Entity\StateTaxRule;
use BillaBear\Entity\TaxType;
use BillaBear\Install\Steps\Tax\DataProvider;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Repository\CountryTaxRuleRepositoryInterface;
use BillaBear\Repository\StateRepositoryInterface;
use BillaBear\Repository\StateTaxRuleRepositoryInterface;
use BillaBear\Repository\TaxTypeRepositoryInterface;
use BillaBear\Tax\ThresholdType;
use Parthenon\Common\Exception\NoEntityFoundException;

readonly class CountryUpdater
{
    public function __construct(
        private DataProvider $dataProvider,
        private CountryRepositoryInterface $countryRepository,
        private StateRepositoryInterface $stateRepository,
        private TaxTypeRepositoryInterface $taxTypeRepository,
        private CountryTaxRuleRepositoryInterface $countryTaxRuleRepository,
        private StateTaxRuleRepositoryInterface $stateTaxRuleRepository,
    ) {
    }

    public function execute(): void
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

            foreach ($countryData['rates'] as $taxTypeStr => $rate) {
                try {
                    $taxType = $this->taxTypeRepository->getByName($taxTypeStr);
                } catch (NoEntityFoundException) {
                    $taxType = new TaxType();
                    $taxType->setName($taxTypeStr);
                    $this->taxTypeRepository->save($taxType);
                }

                try {
                    $taxRule = $this->countryTaxRuleRepository->getOpenEndedForCountryAndTaxType($country, $taxType);
                } catch (NoEntityFoundException) {
                    $taxRule = $this->buildCountryTaxRule($taxType, $country, $rate);
                    $this->countryTaxRuleRepository->save($taxRule);
                }

                if ($rate['rate'] != $taxRule->getTaxRate()) {
                    $taxRule->setValidUntil(new \DateTime('-1 minute'));
                    $this->countryTaxRuleRepository->save($taxRule);

                    $taxRule = $this->buildCountryTaxRule($taxType, $country, $rate);
                    $this->countryTaxRuleRepository->save($taxRule);
                }
            }

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

                    foreach ($stateData['rates'] as $taxTypeStr => $rate) {
                        try {
                            $taxType = $this->taxTypeRepository->getByName($taxTypeStr);
                        } catch (NoEntityFoundException) {
                            $taxType = new TaxType();
                            $taxType->setName($taxTypeStr);
                            $this->taxTypeRepository->save($taxType);
                        }

                        try {
                            $taxRule = $this->stateTaxRuleRepository->getOpenEndedForCountryStateAndTaxType($state, $taxType);
                        } catch (NoEntityFoundException) {
                            $taxRule = $this->buildStateTaxRule($taxType, $state, $rate);
                            $this->stateTaxRuleRepository->save($taxRule);
                        }

                        if ($rate['rate'] != $taxRule->getTaxRate()) {
                            $taxRule->setValidUntil(new \DateTime('-1 minute'));
                            $this->stateTaxRuleRepository->save($taxRule);

                            $taxRule = $this->buildStateTaxRule($taxType, $state, $rate);
                            $this->stateTaxRuleRepository->save($taxRule);
                        }
                    }
                }
            }
        }
    }

    protected function buildStateTaxRule(TaxType $taxType, State $state, array $rate): StateTaxRule
    {
        $taxRule = new StateTaxRule();
        $taxRule->setTaxType($taxType);
        $taxRule->setState($state);
        $taxRule->setCreatedAt(new \DateTime());
        $taxRule->setValidFrom(new \DateTime());
        $taxRule->setIsDefault($rate['default']);
        $taxRule->setTaxRate($rate['rate']);

        return $taxRule;
    }

    protected function buildCountryTaxRule(TaxType $taxType, Country $country, array $rate): CountryTaxRule
    {
        $taxRule = new CountryTaxRule();
        $taxRule->setCountry($country);
        $taxRule->setTaxType($taxType);
        $taxRule->setCreatedAt(new \DateTime());
        $taxRule->setValidFrom(new \DateTime());
        $taxRule->setIsDefault($rate['default']);
        $taxRule->setTaxRate($rate['rate']);

        return $taxRule;
    }
}
