<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Entity;

use Brick\Money\Money;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'checkout_session_line')]
class CheckoutSessionLine implements ConvertableToInvoiceLineInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\ManyToOne(targetEntity: CheckoutSession::class)]
    private CheckoutSession $checkoutSession;

    #[ORM\ManyToOne(targetEntity: SubscriptionPlan::class)]
    private ?SubscriptionPlan $subscriptionPlan = null;

    #[ORM\ManyToOne(targetEntity: Price::class)]
    private ?Price $price = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $seatNumber = null;

    #[ORM\Column(type: 'string')]
    private string $currency;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $total = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $subTotal = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $taxTotal = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $taxPercentage = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: TaxType::class)]
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

    public function getCheckoutSession(): CheckoutSession
    {
        return $this->checkoutSession;
    }

    public function setCheckoutSession(CheckoutSession $checkoutSession): void
    {
        $this->checkoutSession = $checkoutSession;
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

    public function getTaxType(): ?TaxType
    {
        return $this->taxType;
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

    public function getSeatNumber(): ?int
    {
        return $this->seatNumber;
    }

    public function setSeatNumber(?int $seatNumber): void
    {
        $this->seatNumber = $seatNumber;
    }
}
