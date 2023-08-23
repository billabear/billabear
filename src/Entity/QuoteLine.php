<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Entity;

use App\Enum\TaxType;
use Brick\Money\Money;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'quote_line')]
class QuoteLine
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\ManyToOne(targetEntity: Quote::class)]
    private Quote $quote;

    #[ORM\ManyToOne(targetEntity: SubscriptionPlan::class)]
    private ?SubscriptionPlan $subscriptionPlan = null;

    #[ORM\ManyToOne(targetEntity: Price::class)]
    private ?Price $price = null;

    #[ORM\Column(type: 'string')]
    private string $currency;

    #[ORM\Column(type: 'integer')]
    private int $total;

    #[ORM\Column(type: 'integer')]
    private int $subTotal;

    #[ORM\Column(type: 'integer')]
    private int $taxTotal;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $taxPercentage = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(enumType: TaxType::class, nullable: true)]
    protected ?TaxType $taxType = null;

    #[ORM\Column(type: 'boolean')]
    protected bool $includeTax;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $taxCountry;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $reverseCharge = false;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getQuote(): Quote
    {
        return $this->quote;
    }

    public function setQuote(Quote $quote): void
    {
        $this->quote = $quote;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    public function getSubTotal(): int
    {
        return $this->subTotal;
    }

    public function setSubTotal(int $subTotal): void
    {
        $this->subTotal = $subTotal;
    }

    public function getTaxTotal(): int
    {
        return $this->taxTotal;
    }

    public function setTaxTotal(int $taxTotal): void
    {
        $this->taxTotal = $taxTotal;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getTotalMoney(): Money
    {
        return Money::ofMinor($this->total, strtoupper($this->currency));
    }

    public function getVatTotalMoney(): Money
    {
        return Money::ofMinor($this->taxTotal, strtoupper($this->currency));
    }

    public function getSubTotalMoney(): Money
    {
        return Money::ofMinor($this->subTotal, strtoupper($this->currency));
    }

    public function getTaxPercentage(): ?float
    {
        return $this->taxPercentage;
    }

    public function setTaxPercentage(?float $taxPercentage): void
    {
        $this->taxPercentage = $taxPercentage;
    }

    public function getTaxType(): TaxType
    {
        return $this->taxType ?? TaxType::DIGITAL_GOODS;
    }

    public function setTaxType(TaxType $taxType): void
    {
        $this->taxType = $taxType;
    }

    public function getSubscriptionPlan(): ?SubscriptionPlan
    {
        return $this->subscriptionPlan;
    }

    public function setSubscriptionPlan(?SubscriptionPlan $subscriptionPlan): void
    {
        $this->subscriptionPlan = $subscriptionPlan;
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function setPrice(?Price $price): void
    {
        $this->price = $price;
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

    public function isReverseCharge(): bool
    {
        return $this->reverseCharge;
    }

    public function setReverseCharge(bool $reverseCharge): void
    {
        $this->reverseCharge = $reverseCharge;
    }
}
