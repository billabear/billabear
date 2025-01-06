<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'tax_type')]
class TaxType
{
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $name;

    #[ORM\Column(name: 'is_default', type: 'boolean', nullable: true)]
    private ?bool $default = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $vatSenseType = null;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
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
        return true === $this->default;
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
