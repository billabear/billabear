<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity\Processes;

use BillaBear\Entity\Subscription;
use BillaBear\Workflow\WorkflowProcessInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'process_trial_ended')]
class TrialEndedProcess implements WorkflowProcessInterface
{
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

    #[ORM\Column('state', type: 'string')]
    private string $state;

    #[ORM\ManyToOne(targetEntity: Subscription::class)]
    private Subscription $subscription;

    #[ORM\Column('created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column('error', type: 'string', nullable: true, length: 9999)]
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

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(Subscription $subscription): void
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
