<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
