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

namespace App\Dto\Response\App\Country;

use App\Dto\Generic\App\Country;
use Symfony\Component\Serializer\Attribute\SerializedName;

class CountryView
{
    private Country $country;

    #[SerializedName('country_tax_rules')]
    private array $countryTaxRules;

    #[SerializedName('tax_types')]
    private array $taxTypes;

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }

    public function getCountryTaxRules(): array
    {
        return $this->countryTaxRules;
    }

    public function setCountryTaxRules(array $countryTaxRules): void
    {
        $this->countryTaxRules = $countryTaxRules;
    }

    public function getTaxTypes(): array
    {
        return $this->taxTypes;
    }

    public function setTaxTypes(array $taxTypes): void
    {
        $this->taxTypes = $taxTypes;
    }
}
