<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('receipt_line')]
class ReceiptLine extends \Parthenon\Billing\Entity\ReceiptLine
{
    #[ORM\ManyToOne(targetEntity: TaxType::class)]
    protected ?TaxType $taxType = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $taxCountry;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $taxState = null;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $reverseCharge = false;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $metadata = null;

    public function getTaxType(): ?TaxType
    {
        return $this->taxType;
    }

    public function setTaxType(?TaxType $taxType): void
    {
        $this->taxType = $taxType;
    }

    public function isIncludeTax(): bool
    {
        return $this->includeTax;
    }

    public function setIncludeTax(bool $includeTax): void
    {
        $this->includeTax = $includeTax;
    }

    public function getTaxCountry(): ?string
    {
        return $this->taxCountry;
    }

    public function setTaxCountry(?string $taxCountry): void
    {
        $this->taxCountry = $taxCountry;
    }

    public function getTaxState(): ?string
    {
        return $this->taxState;
    }

    public function setTaxState(?string $taxState): void
    {
        $this->taxState = $taxState;
    }

    public function isReverseCharge(): bool
    {
        return $this->reverseCharge;
    }

    public function setReverseCharge(bool $reverseCharge): void
    {
        $this->reverseCharge = $reverseCharge;
    }

    public function getMetadata(): array
    {
        if (!isset($this->metadata)) {
            return [];
        }

        return $this->metadata;
    }

    public function setMetadata(?array $metadata): void
    {
        $this->metadata = $metadata;
    }
}
