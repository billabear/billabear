<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Entity;

use App\Enum\TaxType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('product')]
class Product extends \Parthenon\Billing\Entity\Product
{
    #[ORM\Column(enumType: TaxType::class)]
    protected TaxType $taxType;

    #[ORM\Column(type: 'float', nullable: true)]
    protected ?float $taxRate = null;

    public function getTaxType(): TaxType
    {
        return $this->taxType;
    }

    public function setTaxType(TaxType $taxType): void
    {
        $this->taxType = $taxType;
    }

    public function getTaxRate(): ?float
    {
        return $this->taxRate;
    }

    public function setTaxRate(?float $taxRate): void
    {
        $this->taxRate = $taxRate;
    }
}
