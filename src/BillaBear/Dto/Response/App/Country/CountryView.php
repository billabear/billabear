<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Country;

use BillaBear\Dto\Generic\App\Country;
use Symfony\Component\Serializer\Attribute\SerializedName;

class CountryView
{
    private Country $country;

    #[SerializedName('country_tax_rules')]
    private array $countryTaxRules;

    #[SerializedName('tax_types')]
    private array $taxTypes;

    private array $states;

    public function getStates(): array
    {
        return $this->states;
    }

    public function setStates(array $states): void
    {
        $this->states = $states;
    }

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
