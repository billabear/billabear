<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\BillingAdminInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'vouchers_application')]
class VoucherApplication
{
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

    #[ORM\ManyToOne(targetEntity: Voucher::class)]
    private Voucher $voucher;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private Customer $customer;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?BillingAdminInterface $appliedBy = null;

    #[ORM\Column(type: 'boolean')]
    private bool $used = false;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

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

    public function getVoucher(): Voucher
    {
        return $this->voucher;
    }

    public function setVoucher(Voucher $voucher): void
    {
        $this->voucher = $voucher;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): void
    {
        $this->used = $used;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getAccountingReference(): ?string
    {
        return $this->accountingReference;
    }

    public function setAccountingReference(?string $accountingReference): void
    {
        $this->accountingReference = $accountingReference;
    }
}
