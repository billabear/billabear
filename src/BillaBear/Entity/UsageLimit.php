<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use BillaBear\Pricing\Usage\WarningLevel;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\CustomerInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'usage_limits')]
class UsageLimit
{
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private CustomerInterface $customer;

    #[ORM\Column(type: 'integer')]
    private int $amount;

    #[ORM\Column(type: 'integer', enumType: WarningLevel::class)]
    private WarningLevel $warningLevel;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(CustomerInterface $customer): void
    {
        $this->customer = $customer;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getWarningLevel(): WarningLevel
    {
        return $this->warningLevel;
    }

    public function setWarningLevel(WarningLevel $warningLevel): void
    {
        $this->warningLevel = $warningLevel;
    }
}
