<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Entity;

use App\Enum\CancellationType;
use App\Workflow\WorkflowProcessInterface;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\BillingAdminInterface;
use Parthenon\Billing\Entity\SubscriptionInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'cancellation_requests')]
class CancellationRequest implements WorkflowProcessInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\Column('state', type: 'string')]
    private string $state;

    #[ORM\Column('when_to_cancel', type: 'string')]
    private string $when;

    #[ORM\Column('specific_date', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $specificDate = null;

    #[ORM\Column('cancellation_type', type: 'string', enumType: CancellationType::class, nullable: true)]
    private ?CancellationType $cancellationType = null;

    #[ORM\Column('refund_type', type: 'string')]
    private string $refundType;

    #[ORM\Column('comment', type: 'string', nullable: true)]
    private ?string $comment;

    #[ORM\ManyToOne(targetEntity: SubscriptionInterface::class)]
    private SubscriptionInterface $subscription;

    #[ORM\ManyToOne(targetEntity: BillingAdminInterface::class)]
    private ?BillingAdminInterface $billingAdmin = null;

    #[ORM\Column('original_valid_until', type: 'datetime')]
    private \DateTimeInterface $originalValidUntil;

    #[ORM\Column('created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column('error', type: 'text', nullable: true)]
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

    public function getWhen(): string
    {
        return $this->when;
    }

    public function setWhen(string $when): void
    {
        $this->when = $when;
    }

    public function getSpecificDate(): ?\DateTimeInterface
    {
        return $this->specificDate;
    }

    public function setSpecificDate(?\DateTimeInterface $specificDate): void
    {
        $this->specificDate = $specificDate;
    }

    public function getRefundType(): string
    {
        return $this->refundType;
    }

    public function setRefundType(string $refundType): void
    {
        $this->refundType = $refundType;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    public function getSubscription(): SubscriptionInterface
    {
        return $this->subscription;
    }

    public function setSubscription(SubscriptionInterface $subscription): void
    {
        $this->subscription = $subscription;
    }

    public function getBillingAdmin(): ?BillingAdminInterface
    {
        return $this->billingAdmin;
    }

    public function setBillingAdmin(?BillingAdminInterface $billingAdmin): void
    {
        $this->billingAdmin = $billingAdmin;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getOriginalValidUntil(): \DateTimeInterface
    {
        return $this->originalValidUntil;
    }

    public function setOriginalValidUntil(\DateTimeInterface $originalValidUntil): void
    {
        $this->originalValidUntil = $originalValidUntil;
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

    public function getCancellationType(): ?CancellationType
    {
        return $this->cancellationType;
    }

    public function setCancellationType(?CancellationType $cancellationType): void
    {
        $this->cancellationType = $cancellationType;
    }
}
