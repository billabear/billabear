<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Country;
use BillaBear\Entity\State;
use BillaBear\Entity\StateTaxRule;
use BillaBear\Entity\TaxType;
use Parthenon\Athena\Repository\CrudRepositoryInterface;

interface StateTaxRuleRepositoryInterface extends CrudRepositoryInterface
{
    public function getForCountry(Country $country, State $state): array;

    public function getOpenEndedForCountryStateAndTaxType(Country $country, State $state, TaxType $taxType): StateTaxRule;

    public function getForCountryStateAndTaxType(Country $country, State $state, TaxType $taxType);

    public function getDefaultForCountryStateAndTaxType(Country $country, State $state): ?StateTaxRule;
}
