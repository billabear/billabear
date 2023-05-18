<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'api_key')]
class CreditNote
{
    public const CREATION_TYPE_AUTOMATED = 'automated';
    public const CREATION_TYPE_MANUALLY = 'manually';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private Customer $customer;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $issuer = null;

    #[ORM\Column]
    private string $creationType;

    #[ORM\Column(type: 'int')]
    private int $amount;

    #[ORM\Column(type: 'string')]
    private string $currency;

    #[ORM\Column(type: int)]
    private int $usedAmount;

    #[ORM\Column(nullable: true)]
    private ?string $reason = null;

    #[ORM\Column(type: 'datetimetz')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetimetz')]
    private \DateTime $updatedAt;

    #[ORM\Column(type: 'datetimetz', nullable: true)]
    private ?\DateTime $validUntil = null;

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

    public function getIssuer(): ?User
    {
        return $this->issuer;
    }

    public function setIssuer(?User $issuer): void
    {
        $this->issuer = $issuer;
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
}
