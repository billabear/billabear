<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity\Usage;

use BillaBear\Entity\Customer;
use BillaBear\Entity\UsageLimit;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'usage_warning')]
class UsageWarning
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private Customer $customer;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private UsageLimit $usageLimit;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $startOfPeriod;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $endOfPeriod;

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

    public function getUsageLimit(): UsageLimit
    {
        return $this->usageLimit;
    }

    public function setUsageLimit(UsageLimit $usageLimit): void
    {
        $this->usageLimit = $usageLimit;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getStartOfPeriod(): \DateTime
    {
        return $this->startOfPeriod;
    }

    public function setStartOfPeriod(\DateTime $startOfPeriod): void
    {
        $this->startOfPeriod = $startOfPeriod;
    }

    public function getEndOfPeriod(): \DateTime
    {
        return $this->endOfPeriod;
    }

    public function setEndOfPeriod(\DateTime $endOfPeriod): void
    {
        $this->endOfPeriod = $endOfPeriod;
    }
}
