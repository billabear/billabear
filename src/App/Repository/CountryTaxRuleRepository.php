<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository;

use App\Entity\Country;
use App\Entity\CountryTaxRule;
use App\Entity\TaxType;
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
}
