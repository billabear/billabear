<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Country;
use BillaBear\Entity\CountryTaxRule;
use BillaBear\Entity\State;
use BillaBear\Entity\StateTaxRule;
use BillaBear\Entity\TaxType;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Common\Exception\NoEntityFoundException;

class StateTaxRuleRepository extends DoctrineCrudRepository implements StateTaxRuleRepositoryInterface
{
    public function getForCountry(Country $country, State $state): array
    {
        return $this->entityRepository->findBy(['country' => $country, 'state' => $state]);
    }

    public function getOpenEndedForCountryStateAndTaxType(Country $country, State $state, TaxType $taxType): StateTaxRule
    {
        $stateTaxRule = $this->entityRepository->findOneBy(['country' => $country, 'taxType' => $taxType, 'validUntil' => null]);

        if (!$stateTaxRule instanceof CountryTaxRule) {
            throw new NoEntityFoundException();
        }

        return $stateTaxRule;
    }

    public function getForCountryStateAndTaxType(Country $country, State $state, TaxType $taxType)
    {
        return $this->entityRepository->findBy(['state' => $state, 'taxType' => $taxType]);
    }

    public function getDefaultForCountryStateAndTaxType(Country $country, State $state): ?StateTaxRule
    {
        return $this->entityRepository->findOneBy(['state' => $state, 'isDefault' => true]);
    }
}
