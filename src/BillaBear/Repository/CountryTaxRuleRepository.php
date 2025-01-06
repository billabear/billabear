<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Country;
use BillaBear\Entity\CountryTaxRule;
use BillaBear\Entity\TaxType;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Common\Exception\NoEntityFoundException;

class CountryTaxRuleRepository extends DoctrineCrudRepository implements CountryTaxRuleRepositoryInterface
{
    public function getForCountry(Country $country): array
    {
        return $this->entityRepository->findBy(['country' => $country]);
    }

    public function getOpenEndedForCountryAndTaxType(Country $country, TaxType $taxType): CountryTaxRule
    {
        $countryTaxRule = $this->entityRepository->findOneBy(['country' => $country, 'taxType' => $taxType, 'validUntil' => null]);

        if (!$countryTaxRule instanceof CountryTaxRule) {
            throw new NoEntityFoundException();
        }

        return $countryTaxRule;
    }

    public function getForCountryAndTaxType(Country $country, TaxType $taxType): array
    {
        return $this->entityRepository->findBy(['country' => $country, 'taxType' => $taxType]);
    }

    public function getDefaultForCountryAndTaxType(Country $country): ?CountryTaxRule
    {
        return $this->entityRepository->findOneBy(['country' => $country, 'isDefault' => true]);
    }
}
