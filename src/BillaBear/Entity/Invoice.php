<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Brick\Money\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Common\Address;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Index(name: 'paid_idx', columns: ['paid'])]
#[ORM\Table(name: 'invoice')]
class Invoice
{
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

    #[ORM\Column(type: 'string', unique: true)]
    private string $invoiceNumber;

    #[ORM\Column(type: 'boolean')]
    private bool $valid;

    #[ORM\Embedded(class: Address::class)]
    private Address $billerAddress;

    #[ORM\Embedded(class: Address::class)]
    private Address $payeeAddress;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private Customer $customer;

    #[ORM\ManyToMany(targetEntity: Subscription::class)]
    private array|Collection $subscriptions;

    #[ORM\ManyToMany(targetEntity: Payment::class)]
    private array|Collection $payments;

    #[ORM\OneToMany(targetEntity: InvoicedMetricCounter::class, mappedBy: 'invoice', cascade: ['persist'])]
    private array|Collection $invoicedMetricCounters = [];

    #[ORM\OneToMany(targetEntity: InvoiceLine::class, mappedBy: 'invoice', cascade: ['persist'])]
    private array|Collection $lines;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $comment;

    #[ORM\Column(type: 'string')]
    private string $currency;

    #[ORM\Column(type: 'integer')]
    private int $amountDue;

    #[ORM\Column(type: 'integer')]
    private int $total;

    #[ORM\Column(type: 'integer')]
    private int $subTotal;

    #[ORM\Column(type: 'integer')]
    private int $taxTotal;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $convertedAmountDue = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $convertedTotal = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $convertedSubTotal = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $convertedTaxTotal = null;

    #[ORM\Column(type: 'boolean')]
    private bool $paid = false;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $paidAt = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $updatedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $dueAt = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $accountingReference = null;

    public function __construct()
    {
        $this->payments = new ArrayCollection([]);
        $this->subscriptions = new ArrayCollection([]);
        $this->lines = new ArrayCollection([]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(string $invoiceNumber): void
    {
        $this->invoiceNumber = $invoiceNumber;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getBillerAddress(): Address
    {
        return $this->billerAddress;
    }

    public function setBillerAddress(Address $billerAddress): void
    {
        $this->billerAddress = $billerAddress;
    }

    public function getPayeeAddress(): Address
    {
        return $this->payeeAddress;
    }

    public function setPayeeAddress(Address $payeeAddress): void
    {
        $this->payeeAddress = $payeeAddress;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function addPayment(Payment $payment): void
    {
        $this->payments->add($payment);
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): array|Collection
    {
        return $this->payments;
    }

    public function setPayments(array|Collection $payments): void
    {
        $this->payments = $payments;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
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

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Collection|InvoiceLine[]
     */
    public function getLines(): array|Collection
    {
        return $this->lines;
    }

    public function setLines(array|Collection $lines): void
    {
        $this->lines = $lines;
    }

    /**
     * @return Subscription[]|Collection
     */
    public function getSubscriptions(): array|Collection
    {
        return $this->subscriptions;
    }

    public function setSubscriptions(array|Collection $subscriptions): void
    {
        $this->subscriptions = $subscriptions;
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

    public function isPaid(): bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): void
    {
        $this->paid = $paid;
    }

    public function getPaidAt(): ?\DateTime
    {
        return $this->paidAt;
    }

    public function setPaidAt(\DateTime $paidAt): void
    {
        $this->paidAt = $paidAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getAmountDue(): int
    {
        return $this->amountDue;
    }

    public function setAmountDue(int $amountDue): void
    {
        $this->amountDue = $amountDue;
    }

    public function getAmountDueAsMoney(): Money
    {
        return Money::ofMinor($this->amountDue, strtoupper($this->currency));
    }

    public function getDueAt(): ?\DateTime
    {
        return $this->dueAt;
    }

    public function setDueAt(?\DateTime $dueAt): void
    {
        $this->dueAt = $dueAt;
    }

    public function getBrandSettings(): BrandSettings
    {
        return $this->customer->getBrandSettings();
    }

    public function getAccountingReference(): ?string
    {
        return $this->accountingReference;
    }

    public function setAccountingReference(?string $accountingReference): void
    {
        $this->accountingReference = $accountingReference;
    }

    public function getConvertedAmountDue(): int
    {
        return $this->convertedAmountDue;
    }

    public function setConvertedAmountDue(int $convertedAmountDue): void
    {
        $this->convertedAmountDue = $convertedAmountDue;
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

    /**
     * @return InvoicedMetricCounter[]|Collection
     */
    public function getInvoicedMetricCounters(): Collection
    {
        if (is_array($this->invoicedMetricCounters)) {
            $this->invoicedMetricCounters = new ArrayCollection($this->invoicedMetricCounters);
        }

        return $this->invoicedMetricCounters;
    }

    public function setInvoicedMetricCounters(array|Collection $invoicedMetricCounters): void
    {
        $this->invoicedMetricCounters = $invoicedMetricCounters;
    }

    public function getInvoiceMetricForMetricCounter(Usage\MetricCounter $metricCounter): ?InvoicedMetricCounter
    {
        foreach ($this->getInvoicedMetricCounters() as $invoicedMetricCounter) {
            if ($invoicedMetricCounter->getMetricCounter() === $metricCounter) {
                return $invoicedMetricCounter;
            }
        }

        return null;
    }
}
