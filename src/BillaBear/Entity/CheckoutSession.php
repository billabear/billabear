<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Brick\Money\Money;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\Subscription;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'checkout_session')]
class CheckoutSession implements ConvertableToInvoiceInterface
{
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private ?Customer $customer = null;

    #[ORM\ManyToOne(targetEntity: Checkout::class)]
    private Checkout $checkout;

    #[ORM\Column(type: 'string')]
    private string $currency;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $amountDue = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $total = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $subTotal = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $taxTotal = null;

    #[ORM\OneToMany(targetEntity: CheckoutSessionLine::class, mappedBy: 'checkoutSession', cascade: ['persist'])]
    private array|Collection $lines;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    #[ORM\ManyToMany(targetEntity: Subscription::class)]
    private array|Collection $subscriptions;

    #[ORM\Column(type: 'boolean')]
    private bool $paid = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $paidAt = null;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getAmountDueAsMoney(): Money
    {
        return Money::ofMinor($this->amountDue, $this->currency);
    }

    public function getAmountDue(): ?int
    {
        return $this->amountDue;
    }

    public function setAmountDue(?int $amountDue): void
    {
        $this->amountDue = $amountDue;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(?int $total): void
    {
        $this->total = $total;
    }

    public function getSubTotal(): ?int
    {
        return $this->subTotal;
    }

    public function setSubTotal(?int $subTotal): void
    {
        $this->subTotal = $subTotal;
    }

    public function getTaxTotal(): ?int
    {
        return $this->taxTotal;
    }

    public function setTaxTotal(?int $taxTotal): void
    {
        $this->taxTotal = $taxTotal;
    }

    public function getLines(): array|Collection
    {
        return $this->lines;
    }

    public function setLines(array|Collection $lines): void
    {
        $this->lines = $lines;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCheckout(): Checkout
    {
        return $this->checkout;
    }

    public function setCheckout(Checkout $checkout): void
    {
        $this->checkout = $checkout;
    }

    public function getSubscriptions(): array|Collection
    {
        return $this->subscriptions;
    }

    public function setSubscriptions(array|Collection $subscriptions): void
    {
        $this->subscriptions = $subscriptions;
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

    public function setPaidAt(?\DateTime $paidAt): void
    {
        $this->paidAt = $paidAt;
    }
}
