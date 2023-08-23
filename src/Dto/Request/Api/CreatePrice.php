<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\Api;

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
    private ?string $externalReference = null;

    #[Assert\Type(type: 'boolean')]
    #[SerializedName('recurring')]
    private bool $recurring;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Choice(['week', 'month', 'year'])]
    #[SerializedName('schedule')]
    private ?string $schedule = null;

    #[SerializedName('including_tax')]
    #[Assert\Type(type: 'boolean')]
    private bool $includingTax = true;

    #[SerializedName('public')]
    private bool $public = true;

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

    /**
     * @return string
     */
    public function getExternalReference(): ?string
    {
        return $this->externalReference;
    }

    public function hasExternalReference(): bool
    {
        return isset($this->externalReference);
    }

    public function setExternalReference(?string $externalReference): void
    {
        $this->externalReference = $externalReference;
    }

    public function setRecurring(bool $recurring): void
    {
        $this->recurring = $recurring;
    }

    public function setSchedule(?string $schedule): void
    {
        $this->schedule = $schedule;
    }

    public function setIncludingTax(bool $includingTax): void
    {
        $this->includingTax = $includingTax;
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
        return $this->includingTax;
    }
}
