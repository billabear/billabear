<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Entity;

use App\Enum\SubscriptionSeatModificationType;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\SubscriptionInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'subscription_seat_modification')]
class SubscriptionSeatModification
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\Column(type: 'string', enumType: SubscriptionSeatModificationType::class)]
    private SubscriptionSeatModificationType $type;

    #[ORM\Column(type: 'integer')]
    private int $changeValue;

    #[ORM\ManyToOne(targetEntity: SubscriptionInterface::class)]
    private SubscriptionInterface $subscription;

    #[ORM\Column('created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getType(): SubscriptionSeatModificationType
    {
        return $this->type;
    }

    public function setType(SubscriptionSeatModificationType $type): void
    {
        $this->type = $type;
    }

    public function getSubscription(): SubscriptionInterface
    {
        return $this->subscription;
    }

    public function setSubscription(SubscriptionInterface $subscription): void
    {
        $this->subscription = $subscription;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getChangeValue(): int
    {
        return $this->changeValue;
    }

    public function setChangeValue(int $changeValue): void
    {
        $this->changeValue = $changeValue;
    }
}
