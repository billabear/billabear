<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\Subscription\MassChange;

use App\Dto\Generic\App\Price;
use App\Dto\Generic\App\SubscriptionPlan;
use App\Dto\Response\App\BrandSettings\BrandSettings;
use Symfony\Component\Serializer\Annotation\SerializedName;

class MassSubscriptionChange
{
    private string $id;

    #[SerializedName('change_date')]
    private \DateTime $changeDate;

    private string $status;

    #[SerializedName('target_plan')]
    private ?SubscriptionPlan $targetPlan = null;

    #[SerializedName('new_plan')]
    private ?SubscriptionPlan $newPlan = null;

    #[SerializedName('target_price')]
    private ?Price $targetPrice = null;

    #[SerializedName('new_price')]
    private ?Price $newPrice = null;

    #[SerializedName('target_country')]
    private ?string $targetCountry = null;

    #[SerializedName('target_brand')]
    private ?BrandSettings $targetBrandSettings = null;

    #[SerializedName('created_at')]
    private \DateTime $createdAt;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getTargetPlan(): ?SubscriptionPlan
    {
        return $this->targetPlan;
    }

    public function setTargetPlan(?SubscriptionPlan $targetPlan): void
    {
        $this->targetPlan = $targetPlan;
    }

    public function getNewPlan(): ?SubscriptionPlan
    {
        return $this->newPlan;
    }

    public function setNewPlan(?SubscriptionPlan $newPlan): void
    {
        $this->newPlan = $newPlan;
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

    public function getTargetCountry(): ?string
    {
        return $this->targetCountry;
    }

    public function setTargetCountry(?string $targetCountry): void
    {
        $this->targetCountry = $targetCountry;
    }

    public function getTargetBrandSettings(): ?BrandSettings
    {
        return $this->targetBrandSettings;
    }

    public function setTargetBrandSettings(?BrandSettings $targetBrandSettings): void
    {
        $this->targetBrandSettings = $targetBrandSettings;
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
