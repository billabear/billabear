<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
