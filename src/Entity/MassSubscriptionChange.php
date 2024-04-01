<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Entity;

use App\Enum\MassSubscriptionChangeStatus;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\BillingAdminInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'mass_subscription_change')]
class MassSubscriptionChange
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $changeDate;

    #[ORM\ManyToOne(targetEntity: SubscriptionPlan::class)]
    private ?SubscriptionPlan $targetSubscriptionPlan = null;

    #[ORM\ManyToOne(targetEntity: SubscriptionPlan::class)]
    private ?SubscriptionPlan $newSubscriptionPlan = null;

    #[ORM\ManyToOne(targetEntity: BillingAdminInterface::class)]
    private BillingAdminInterface $createdBy;

    #[ORM\ManyToOne(targetEntity: Price::class)]
    private ?Price $targetPrice = null;

    #[ORM\ManyToOne(targetEntity: Price::class)]
    private ?Price $newPrice = null;

    #[ORM\ManyToOne(targetEntity: BrandSettings::class)]
    private ?BrandSettings $brandSettings = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $targetCountry = null;

    #[ORM\Column(enumType: MassSubscriptionChangeStatus::class)]
    private MassSubscriptionChangeStatus $status;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getChangeDate(): \DateTime
    {
        return $this->changeDate;
    }

    public function setChangeDate(\DateTime $changeDate): void
    {
        $this->changeDate = $changeDate;
    }

    public function getTargetSubscriptionPlan(): ?SubscriptionPlan
    {
        return $this->targetSubscriptionPlan;
    }

    public function setTargetSubscriptionPlan(?SubscriptionPlan $targetSubscriptionPlan): void
    {
        $this->targetSubscriptionPlan = $targetSubscriptionPlan;
    }

    public function getNewSubscriptionPlan(): ?SubscriptionPlan
    {
        return $this->newSubscriptionPlan;
    }

    public function setNewSubscriptionPlan(?SubscriptionPlan $newSubscriptionPlan): void
    {
        $this->newSubscriptionPlan = $newSubscriptionPlan;
    }

    public function getCreatedBy(): BillingAdminInterface
    {
        return $this->createdBy;
    }

    public function setCreatedBy(BillingAdminInterface $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getTargetPrice(): ?Price
    {
        return $this->targetPrice;
    }

    public function setTargetPrice(?Price $targetPrice): void
    {
        $this->targetPrice = $targetPrice;
    }

    public function getNewPrice(): ?Price
    {
        return $this->newPrice;
    }

    public function setNewPrice(?Price $newPrice): void
    {
        $this->newPrice = $newPrice;
    }

    public function getBrandSettings(): ?BrandSettings
    {
        return $this->brandSettings;
    }

    public function setBrandSettings(?BrandSettings $brandSettings): void
    {
        $this->brandSettings = $brandSettings;
    }

    public function getTargetCountry(): ?string
    {
        return $this->targetCountry;
    }

    public function setTargetCountry(?string $targetCountry): void
    {
        $this->targetCountry = $targetCountry;
    }

    public function getStatus(): MassSubscriptionChangeStatus
    {
        return $this->status;
    }

    public function setStatus(MassSubscriptionChangeStatus $status): void
    {
        $this->status = $status;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
