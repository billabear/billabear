<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('product')]
class Product extends \Parthenon\Billing\Entity\Product
{
    #[ORM\ManyToOne(targetEntity: TaxType::class)]
    protected ?TaxType $taxType = null;

    #[ORM\Column(type: 'float', nullable: true)]
    protected ?float $taxRate = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    protected ?bool $physical = false;

    public function getTaxType(): ?TaxType
    {
        return $this->taxType;
    }

    public function setTaxType(?TaxType $taxType): void
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

    public function getPhysical(): bool
    {
        return true === $this->physical;
    }

    public function setPhysical(?bool $physical): void
    {
        $this->physical = $physical;
    }
}
