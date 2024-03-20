<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Parthenon\Athena\Entity\DeletableInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'country_tax_rule')]
class CountryTaxRule implements DeletableInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    private Country $country;

    #[ORM\ManyToOne(targetEntity: TaxType::class)]
    private TaxType $taxType;

    #[ORM\Column(type: 'float')]
    private float $taxRate;

    #[ORM\Column(type: 'boolean')]
    private bool $isDefault;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $validFrom;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $validUntil = null;

    #[ORM\Column(type: 'boolean')]
    protected bool $deleted = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTimeInterface $deletedAt = null;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }

    public function getTaxType(): TaxType
    {
        return $this->taxType;
    }

    public function setTaxType(TaxType $taxType): void
    {
        $this->taxType = $taxType;
    }

    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    public function setTaxRate(float $taxRate): void
    {
        $this->taxRate = $taxRate;
    }

    public function isIsDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getValidFrom(): \DateTime
    {
        return $this->validFrom;
    }

    public function setValidFrom(\DateTime $validFrom): void
    {
        $this->validFrom = $validFrom;
    }

    public function getValidUntil(): ?\DateTime
    {
        return $this->validUntil;
    }

    public function setValidUntil(?\DateTime $validUntil): void
    {
        $this->validUntil = $validUntil;
    }

    public function setDeletedAt(\DateTimeInterface $dateTime): DeletableInterface
    {
        $this->deletedAt = $dateTime;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function markAsDeleted(): DeletableInterface
    {
        $this->deletedAt = new \DateTime('now');
        $this->deleted = true;

        return $this;
    }

    public function unmarkAsDeleted(): DeletableInterface
    {
        $this->deletedAt = null;
        $this->deleted = false;

        return $this;
    }
}
