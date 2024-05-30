<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
        foreach ($this->countryList->getCountryList() as $countryData) {
            $country = new Country();
            $country->setName($countryData['name']);
            $country->setIsoCode($countryData['code']);
            $country->setCurrency($countryData['currency']);
            $country->setThreshold($countryData['threshold']);
            $country->setInEu($countryData['in_eu']);
            $country->setEnabled(true);
            $country->setCreatedAt(new \DateTime());

            $this->countryRepository->save($country);

            $rates = $countryData['rates'] ?? [];
            foreach ($rates as $name => $data) {
                try {
                    $taxType = $this->taxTypeRepository->getByName($name);
                } catch (NoEntityFoundException) {
                    $taxType = new TaxType();
                    $taxType->setName($name);
                    $taxType->setDefault(true);

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
                $state->setName($code);
                $state->setThreshold($data['threshold']);
                $state->setHasNexus(false);

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
                    $stateTaxRule->setIsDefault($data['default']);
                    $stateTaxRule->setCreatedAt(new \DateTime());
                    $stateTaxRule->setValidFrom(new \DateTime());

                    $this->stateTaxRuleRepository->save($stateTaxRule);
                }
            }
        }
    }
}
