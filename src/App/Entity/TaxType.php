<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'tax_type')]
class TaxType
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $name;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private ?bool $physical;

    #[ORM\Column(name: 'is_default', type: 'boolean', nullable: true)]
    private ?bool $default = null;

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

    public function isPhysical(): bool
    {
        return $this->physical;
    }

    public function setPhysical(bool $physical): void
    {
        $this->physical = $physical;
    }

    public function isDefault(): bool
    {
        return true === $this->default;
    }

    public function setDefault(bool $default): void
    {
        $this->default = $default;
    }
}
