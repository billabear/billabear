<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Brick\Money\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Index(fields: ['customer'])]
#[ORM\Table(name: 'payment_attempt')]
class PaymentAttempt
{
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private Customer $customer;

    #[ORM\ManyToOne(targetEntity: Invoice::class)]
    private ?Invoice $invoice = null;

    #[ORM\ManyToMany(targetEntity: Subscription::class)]
    private array|Collection $subscriptions;

    #[ORM\Column(type: 'string')]
    private string $failureReason;

    #[ORM\Column(type: 'integer')]
    private int $amount;

    #[ORM\Column(type: 'string')]
    private string $currency;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection([]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): void
    {
        $this->invoice = $invoice;
    }

    public function getFailureReason(): string
    {
        return $this->failureReason;
    }

    public function setFailureReason(string $failureReason): void
    {
        $this->failureReason = $failureReason;
    }

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

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Collection|Subscription[]
     */
    public function getSubscriptions(): array|Collection
    {
        return $this->subscriptions;
    }

    public function setSubscriptions(array|Collection $subscriptions): void
    {
        $this->subscriptions = $subscriptions;
    }

    public function getMoneyAmount(): Money
    {
        return Money::ofMinor($this->amount, $this->currency);
    }
}
