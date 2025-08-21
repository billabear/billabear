<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\State;
use BillaBear\Entity\StateTaxRule;
use BillaBear\Entity\TaxType;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Common\Exception\NoEntityFoundException;

class StateTaxRuleRepository extends DoctrineCrudRepository implements StateTaxRuleRepositoryInterface
{
    public function getForState(State $state): array
    {
        return $this->entityRepository->findBy(['state' => $state]);
    }

    public function getOpenEndedForCountryStateAndTaxType(State $state, TaxType $taxType): StateTaxRule
    {
        $stateTaxRule = $this->entityRepository->findOneBy(['state' => $state, 'taxType' => $taxType, 'validUntil' => null]);

        if (!$stateTaxRule instanceof StateTaxRule) {
            throw new NoEntityFoundException();
        }

        return $stateTaxRule;
    }

    public function getForCountryStateAndTaxType(State $state, TaxType $taxType)
    {
        return $this->entityRepository->findBy(['state' => $state, 'taxType' => $taxType]);
    }

    public function getDefaultForCountryStateAndTaxType(State $state): ?StateTaxRule
    {
        return $this->entityRepository->findOneBy(['state' => $state, 'isDefault' => true]);
    }
}
