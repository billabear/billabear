<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Country;
use BillaBear\Entity\CountryTaxRule;
use BillaBear\Entity\TaxType;
use Parthenon\Athena\Repository\CrudRepositoryInterface;

interface CountryTaxRuleRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @return CountryTaxRule[]
     */
    public function getForCountry(Country $country): array;

    public function getOpenEndedForCountryAndTaxType(Country $country, TaxType $taxType): CountryTaxRule;

    /**
     * @return CountryTaxRule[]
     */
    public function getForCountryAndTaxType(Country $country, TaxType $taxType): array;

    public function getDefaultForCountryAndTaxType(Country $country): ?CountryTaxRule;
}
