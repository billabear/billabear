<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use BillaBear\Tax\ThresholdType;
use Brick\Money\Money;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'state')]
class State
{
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string')]
    private string $code;

    #[ORM\Column(type: 'bigint')]
    private int $threshold;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $transactionThreshold = null;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    private Country $country;

    #[ORM\Column(type: 'boolean')]
    private bool $collecting;

    #[ORM\Column(type: 'string', enumType: ThresholdType::class, nullable: true)]
    private ?ThresholdType $thresholdType = null;

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

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getThreshold(): int
    {
        return $this->threshold;
    }

    public function setThreshold(int $threshold): void
    {
        $this->threshold = $threshold;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }

    public function isCollecting(): bool
    {
        return $this->collecting;
    }

    public function setCollecting(bool $collecting): void
    {
        $this->collecting = $collecting;
    }

    public function getThresholdAsMoney(): Money
    {
        return Money::ofMinor($this->threshold, $this->country->getCurrency());
    }

    public function getTransactionThreshold(): ?int
    {
        return $this->transactionThreshold;
    }

    public function setTransactionThreshold(?int $transactionThreshold): void
    {
        $this->transactionThreshold = $transactionThreshold;
    }

    public function getThresholdType(): ?ThresholdType
    {
        return $this->thresholdType;
    }

    public function setThresholdType(?ThresholdType $thresholdType): void
    {
        $this->thresholdType = $thresholdType;
    }
}
