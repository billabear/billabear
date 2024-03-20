<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Install\Steps\Tax;

use App\Entity\Country;
use App\Entity\CountryTaxRule;
use App\Entity\TaxType;
use App\Repository\CountryRepositoryInterface;
use App\Repository\CountryTaxRuleRepositoryInterface;
use App\Repository\TaxTypeRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class TaxDataCreator
{
    public function __construct(
        private DataProvider $countryList,
        private CountryRepositoryInterface $countryRepository,
        private CountryTaxRuleRepositoryInterface $countryTaxRuleRepository,
        private TaxTypeRepositoryInterface $taxTypeRepository,
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
            $country->setRevenueForTaxYear(0);
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
                    $taxType->setPhysical(false); // remove

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
        }
    }
}
