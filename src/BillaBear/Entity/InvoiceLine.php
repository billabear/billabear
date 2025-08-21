<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Brick\Money\Money;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'invoice_line')]
class InvoiceLine
{
    #[ORM\ManyToOne(targetEntity: TaxType::class)]
    protected ?TaxType $taxType = null;
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

    #[ORM\ManyToOne(targetEntity: Invoice::class)]
    private Invoice $invoice;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private ?Product $product = null;

    #[ORM\ManyToOne(targetEntity: Subscription::class)]
    private ?Subscription $subscription = null;

    #[ORM\ManyToOne(targetEntity: InvoicedMetricCounter::class, cascade: ['persist'])]
    private ?InvoicedMetricCounter $invoicedMetricCounter = null;

    #[ORM\Column(type: 'string')]
    private string $currency;

    #[ORM\Column(type: 'integer')]
    private int $netPrice;

    #[ORM\Column(type: 'integer')]
    private int $total;

    #[ORM\Column(type: 'integer')]
    private int $subTotal;

    #[ORM\Column(type: 'integer')]
    private int $taxTotal;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $convertedNetPrice = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $convertedTotal = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $convertedSubTotal = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $convertedTaxTotal = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $taxPercentage = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $taxCountry;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $taxState = null;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $reverseCharge = false;

    #[ORM\Column(type: 'float', nullable: true)]
    private float $quantity;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $metadata = null;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(Invoice $invoice): void
    {
        $this->invoice = $invoice;
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

    public function getTaxTotalAsMoney(): Money
    {
        return Money::ofMinor($this->taxTotal, $this->currency);
    }

    public function getDescription(): string
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

    public function getNetPrice(): int
    {
        return $this->netPrice;
    }

    public function setNetPrice(int $netPrice): void
    {
        $this->netPrice = $netPrice;
    }

    public function getNetPriceAsMoney(): Money
    {
        return Money::ofMinor($this->netPrice, strtoupper($this->currency));
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

    public function setTaxType(?TaxType $taxType): void
    {
        $this->taxType = $taxType;
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

    public function getQuantity(): float
    {
        if (!isset($this->quantity)) {
            return 1;
        }

        return $this->quantity;
    }

    public function setQuantity(float $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): void
    {
        $this->product = $product;
    }

    public function isZeroRated(): bool
    {
        if (0 === $this->taxPercentage || 0.0 === $this->taxPercentage) {
            return true;
        }

        return false;
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

    public function getConvertedNetPrice(): int
    {
        return $this->convertedNetPrice;
    }

    public function setConvertedNetPrice(int $convertedNetPrice): void
    {
        $this->convertedNetPrice = $convertedNetPrice;
    }

    public function getConvertedTotal(): int
    {
        return $this->convertedTotal;
    }

    public function setConvertedTotal(int $convertedTotal): void
    {
        $this->convertedTotal = $convertedTotal;
    }

    public function getConvertedSubTotal(): int
    {
        return $this->convertedSubTotal;
    }

    public function setConvertedSubTotal(int $convertedSubTotal): void
    {
        $this->convertedSubTotal = $convertedSubTotal;
    }

    public function getConvertedTaxTotal(): int
    {
        return $this->convertedTaxTotal;
    }

    public function setConvertedTaxTotal(int $convertedTaxTotal): void
    {
        $this->convertedTaxTotal = $convertedTaxTotal;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): void
    {
        $this->subscription = $subscription;
    }

    public function getInvoicedMetricCounter(): ?InvoicedMetricCounter
    {
        return $this->invoicedMetricCounter;
    }

    public function setInvoicedMetricCounter(?InvoicedMetricCounter $invoicedMetricCounter): void
    {
        $this->invoicedMetricCounter = $invoicedMetricCounter;
    }
}
