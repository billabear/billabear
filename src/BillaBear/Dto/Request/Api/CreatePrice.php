<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Api;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePrice
{
    #[Assert\NotBlank()]
    #[Assert\Type(type: 'integer')]
    #[Assert\Positive]
    #[SerializedName('amount')]
    private $amount;

    #[Assert\NotBlank()]
    #[Assert\Currency]
    #[SerializedName('currency')]
    private $currency;

    #[Assert\NotBlank(allowNull: true)]
    #[SerializedName('external_reference')]
    private $external_reference;

    #[Assert\Type(type: 'boolean')]
    #[SerializedName('recurring')]
    private $recurring;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Choice(['week', 'month', 'year'])]
    #[SerializedName('schedule')]
    private $schedule;

    #[SerializedName('including_tax')]
    #[Assert\Type(type: 'boolean')]
    private $including_tax;

    #[SerializedName('public')]
    private $public = true;

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): void
    {
        $this->public = $public;
    }

    public function getExternalReference(): ?string
    {
        return $this->external_reference;
    }

    public function hasExternalReference(): bool
    {
        return isset($this->external_reference);
    }

    public function setExternalReference(?string $external_reference): void
    {
        $this->external_reference = $external_reference;
    }

    public function setRecurring(bool $recurring): void
    {
        $this->recurring = $recurring;
    }

    public function setSchedule(?string $schedule): void
    {
        $this->schedule = $schedule;
    }

    public function setIncludingTax(bool $including_tax): void
    {
        $this->including_tax = $including_tax;
    }

    public function isRecurring(): bool
    {
        return $this->recurring;
    }

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    public function isIncludingTax(): bool
    {
        return true === $this->including_tax;
    }
}
