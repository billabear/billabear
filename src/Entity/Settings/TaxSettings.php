<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Entity\Settings;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class TaxSettings
{
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $taxCustomersWithTaxNumbers = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $europeanBusinessTaxRules = null;

    public function getTaxCustomersWithTaxNumbers(): bool
    {
        return true === $this->taxCustomersWithTaxNumbers;
    }

    public function setTaxCustomersWithTaxNumbers(?bool $taxCustomersWithTaxNumbers): void
    {
        $this->taxCustomersWithTaxNumbers = $taxCustomersWithTaxNumbers;
    }

    public function getEuropeanBusinessTaxRules(): bool
    {
        return true === $this->europeanBusinessTaxRules;
    }

    public function setEuropeanBusinessTaxRules(?bool $europeanBusinessTaxRules): void
    {
        $this->europeanBusinessTaxRules = $europeanBusinessTaxRules;
    }
}
