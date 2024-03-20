<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\Settings\Tax;

use Symfony\Component\Serializer\Annotation\SerializedName;

class TaxSettings
{
    #[SerializedName('tax_customers_with_tax_number')]
    private bool $taxCustomersWithTaxNumber;

    #[SerializedName('eu_business_tax_rules')]
    private bool $euBusinessTaxRules;

    public function getTaxCustomersWithTaxNumber(): bool
    {
        return $this->taxCustomersWithTaxNumber;
    }

    public function setTaxCustomersWithTaxNumber(bool $taxCustomersWithTaxNumber): void
    {
        $this->taxCustomersWithTaxNumber = $taxCustomersWithTaxNumber;
    }

    public function isEuBusinessTaxRules(): bool
    {
        return $this->euBusinessTaxRules;
    }

    public function setEuBusinessTaxRules(bool $euBusinessTaxRules): void
    {
        $this->euBusinessTaxRules = $euBusinessTaxRules;
    }
}
