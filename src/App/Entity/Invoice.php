<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Entity;

use Brick\Money\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Common\Address;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'invoice')]
class Invoice
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
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

    #[ORM\Column(type: 'boolean')]
    private bool $paid = false;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $paidAt = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dueAt = null;

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
    public function getPayments(): Collection|array
    {
        return $this->payments;
    }

    public function setPayments(Collection|array $payments): void
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

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Collection|InvoiceLine[]
     */
    public function getLines(): Collection|array
    {
        return $this->lines;
    }

    public function setLines(Collection|array $lines): void
    {
        $this->lines = $lines;
    }

    /**
     * @return Subscription[]|Collection
     */
    public function getSubscriptions(): Collection|array
    {
        return $this->subscriptions;
    }

    public function setSubscriptions(Collection|array $subscriptions): void
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

    public function getVatPercentage(): float
    {
        return $this->vatPercentage;
    }

    public function setVatPercentage(float $vatPercentage): void
    {
        $this->vatPercentage = $vatPercentage;
    }

    public function isPaid(): bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): void
    {
        $this->paid = $paid;
    }

    public function getPaidAt(): ?\DateTimeInterface
    {
        return $this->paidAt;
    }

    public function setPaidAt(\DateTimeInterface $paidAt): void
    {
        $this->paidAt = $paidAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
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

    public function getDueAt(): ?\DateTimeInterface
    {
        return $this->dueAt;
    }

    public function setDueAt(?\DateTimeInterface $dueAt): void
    {
        $this->dueAt = $dueAt;
    }
}
