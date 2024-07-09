<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\App;

use Symfony\Component\Serializer\Attribute\SerializedName;

class Country
{
    private string $id;

    private string $name;

    private bool $enabled;

    #[SerializedName('iso_code')]
    private string $isoCode;

    #[SerializedName('iso_code_3')]
    private string $isoCode3;

    private string $currency;

    private int $threshold;

    #[SerializedName('in_eu')]
    private bool $inEu;

    #[SerializedName('amount_transacted')]
    private int $amountTransacted;

    #[SerializedName('start_of_tax_year')]
    private ?string $startOfTaxYear;

    private bool $collecting;

    #[SerializedName('tax_number')]
    private ?string $taxNumber;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
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

    public function isInEu(): bool
    {
        return $this->inEu;
    }

    public function setInEu(bool $inEu): void
    {
        $this->inEu = $inEu;
    }

    public function getAmountTransacted(): int
    {
        return $this->amountTransacted;
    }

    public function setAmountTransacted(int $amountTransacted): void
    {
        $this->amountTransacted = $amountTransacted;
    }

    public function getStartOfTaxYear(): ?string
    {
        return $this->startOfTaxYear;
    }

    public function setStartOfTaxYear(?string $startOfTaxYear): void
    {
        $this->startOfTaxYear = $startOfTaxYear;
    }

    public function getIsoCode3(): string
    {
        return $this->isoCode3;
    }

    public function setIsoCode3(string $isoCode3): void
    {
        $this->isoCode3 = $isoCode3;
    }

    public function isCollecting(): bool
    {
        return $this->collecting;
    }

    public function setCollecting(bool $collecting): void
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
