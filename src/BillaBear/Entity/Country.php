<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Brick\Money\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'country')]
class Country
{
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string')]
    private string $isoCode;

    #[ORM\Column(type: 'string')]
    private string $currency;

    #[ORM\Column(type: 'bigint')]
    private int $threshold;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $startOfTaxYear = null;

    #[ORM\Column(type: 'boolean')]
    private bool $inEu;

    #[ORM\Column(type: 'boolean')]
    private bool $enabled;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\OneToMany(mappedBy: 'country', targetEntity: State::class)]
    private array|Collection $states;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $collecting = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $taxNumber = null;

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

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    public function setIsoCode(string $isoCode): void
    {
        $this->isoCode = $isoCode;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getThreshold(): int
    {
        return $this->threshold;
    }

    public function setThreshold(int $threshold): void
    {
        $this->threshold = $threshold;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getStartOfTaxYear(): ?string
    {
        return $this->startOfTaxYear;
    }

    public function setStartOfTaxYear(?string $startOfTaxYear): void
    {
        $this->startOfTaxYear = $startOfTaxYear;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function isInEu(): bool
    {
        return $this->inEu;
    }

    public function setInEu(bool $inEu): void
    {
        $this->inEu = $inEu;
    }

    public function getThresholdAsMoney(): Money
    {
        return Money::ofMinor($this->threshold, $this->currency);
    }

    public function getStates(): Collection
    {
        if (is_array($this->states)) {
            return new ArrayCollection($this->states);
        }

        return $this->states;
    }

    public function setStates(array|Collection $states): void
    {
        $this->states = $states;
    }

    public function getCollecting(): bool
    {
        return true === $this->collecting;
    }

    public function setCollecting(?bool $collecting): void
    {
        $this->collecting = $collecting;
    }

    public function getTaxNumber(): ?string
    {
        return $this->taxNumber;
    }

    public function setTaxNumber(?string $taxNumber): void
    {
        $this->taxNumber = $taxNumber;
    }
}
