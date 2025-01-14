<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\App;

use BillaBear\Tax\ThresholdType;
use Symfony\Component\Serializer\Attribute\SerializedName;

class State
{
    private string $id;

    private string $name;

    private string $code;

    private int $threshold;

    #[SerializedName('transaction_threshold')]
    private ?int $transactionThreshold;

    #[SerializedName('threshold_type')]
    private ThresholdType $thresholdType;

    private bool $collecting;

    private Country $country;

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

    public function isCollecting(): bool
    {
        return $this->collecting;
    }

    public function setCollecting(bool $collecting): void
    {
        $this->collecting = $collecting;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }

    public function getTransactionThreshold(): ?int
    {
        return $this->transactionThreshold;
    }

    public function setTransactionThreshold(?int $transactionThreshold): void
    {
        $this->transactionThreshold = $transactionThreshold;
    }

    public function getThresholdType(): ThresholdType
    {
        return $this->thresholdType;
    }

    public function setThresholdType(ThresholdType $thresholdType): void
    {
        $this->thresholdType = $thresholdType;
    }
}
