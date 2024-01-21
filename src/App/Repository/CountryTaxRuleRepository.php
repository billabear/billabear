<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
