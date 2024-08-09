<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\App;

use Symfony\Component\Serializer\Attribute\SerializedName;

class TaxType
{
    private string $id;

    private string $name;

    private bool $default;

    #[SerializedName('vat_sense_type')]
    private ?string $vatSenseType;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    public function setDefault(bool $default): void
    {
        $this->default = $default;
    }

    public function getVatSenseType(): ?string
    {
        return $this->vatSenseType;
    }

    public function setVatSenseType(?string $vatSenseType): void
    {
        $this->vatSenseType = $vatSenseType;
    }
}
