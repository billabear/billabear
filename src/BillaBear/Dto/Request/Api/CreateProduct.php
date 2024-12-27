<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Api;

use BillaBear\Validator\Constraints\TaxTypeExists;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateProduct
{
    #[Assert\NotBlank]
    #[SerializedName('name')]
    private string $name;

    #[SerializedName('external_reference')]
    private ?string $external_reference = null;

    #[Assert\NotBlank(allowNull: true)]
    #[SerializedName('tax_type')]
    #[TaxTypeExists]
    private $tax_type;

    #[Assert\Type('boolean')]
    private $physical = false;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getExternalReference(): ?string
    {
        return $this->external_reference;
    }

    public function setExternalReference(?string $external_reference): void
    {
        $this->external_reference = $external_reference;
    }

    public function getTaxType()
    {
        return $this->tax_type;
    }

    public function setTaxType($tax_type): void
    {
        $this->tax_type = $tax_type;
    }

    public function isPhysical(): bool
    {
        return $this->physical;
    }

    public function setPhysical(bool $physical): void
    {
        $this->physical = $physical;
    }
}
