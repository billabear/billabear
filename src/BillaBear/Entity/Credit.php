<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Brick\Money\Money;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Index(name: 'customer_idx', fields: ['customer'])]
#[ORM\Table(name: 'credit')]
class Credit
{
    public const CREATION_TYPE_AUTOMATED = 'automated';
    public const CREATION_TYPE_MANUALLY = 'manually';
    public const TYPE_CREDIT = 'credit';
    public const TYPE_DEBIT = 'debit';

    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private Customer $customer;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $billingAdmin = null;

    #[ORM\Column]
    private string $type = self::TYPE_CREDIT;

    #[ORM\Column]
    private string $creationType;

    #[ORM\Column(type: 'integer')]
    private int $amount;

    #[ORM\Column(type: 'string')]
    private string $currency;

    #[ORM\Column(type: 'integer')]
    private int $usedAmount;

    #[ORM\Column(type: 'boolean')]
    private bool $completelyUsed = false;

    #[ORM\Column(nullable: true)]
    private ?string $reason = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $updatedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $validUntil = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $accountingReference = null;

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

    public function getBillingAdmin(): ?User
    {
        return $this->billingAdmin;
    }

    public function setBillingAdmin(?User $billingAdmin): void
    {
        $this->billingAdmin = $billingAdmin;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getAmountAsMoney(): Money
    {
        return Money::ofMinor($this->amount, $this->currency);
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getUsedAmount(): int
    {
        return $this->usedAmount;
    }

    public function setUsedAmount(int $usedAmount): void
    {
        $this->usedAmount = $usedAmount;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): void
    {
        $this->reason = $reason;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getValidUntil(): ?\DateTime
    {
        return $this->validUntil;
    }

    public function setValidUntil(?\DateTime $validUntil): void
    {
        $this->validUntil = $validUntil;
    }

    public function getCreationType(): string
    {
        return $this->creationType;
    }

    public function setCreationType(string $creationType): void
    {
        $this->creationType = $creationType;
    }

    public function isCompletelyUsed(): bool
    {
        return $this->completelyUsed;
    }

    public function setCompletelyUsed(bool $completelyUsed): void
    {
        $this->completelyUsed = $completelyUsed;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getAccountingReference(): ?string
    {
        return $this->accountingReference;
    }

    public function setAccountingReference(?string $accountingReference): void
    {
        $this->accountingReference = $accountingReference;
    }

    public function asMoney(): Money
    {
        $multipler = (Credit::TYPE_DEBIT === $this->type) ? -1 : 1;
        $amount = $this->amount * $multipler;

        return Money::ofMinor($amount, $this->currency);
    }

    public function isCredit(): bool
    {
        return self::TYPE_CREDIT === $this->type;
    }
}
