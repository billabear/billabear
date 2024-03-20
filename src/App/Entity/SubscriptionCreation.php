<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Entity;

use App\Workflow\WorkflowProcessInterface;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\SubscriptionInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'subscription_creation')]
class SubscriptionCreation implements WorkflowProcessInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\Column('state', type: 'string')]
    private string $state;

    #[ORM\ManyToOne(targetEntity: SubscriptionInterface::class)]
    private SubscriptionInterface $subscription;

    #[ORM\Column('created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column('error', type: 'string', nullable: true)]
    private ?string $error = null;

    #[ORM\Column('has_error', type: 'boolean', nullable: true)]
    private ?bool $hasError = false;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
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

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): void
    {
        $this->error = $error;
    }

    public function getHasError(): bool
    {
        return true === $this->hasError;
    }

    public function setHasError(?bool $hasError): void
    {
        $this->hasError = $hasError;
    }
}
