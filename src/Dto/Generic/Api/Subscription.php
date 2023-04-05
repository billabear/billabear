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

namespace App\Dto\Generic\Api;

use App\Dto\Generic\App\Price;
use App\Dto\Generic\App\SubscriptionPlan;

class Subscription
{
    private string $id;

    private string $schedule;

    private \DateTimeInterface $createdAt;

    private \DateTimeInterface $updatedAt;

    private ?\DateTimeInterface $endedAt = null;

    private \DateTimeInterface $validUntil;

    private string $externalMainReference;

    private ?string $externalMainReferenceDetailsUrl = null;

    private string $childExternalReference;

    private SubscriptionPlan $subscriptionPlan;

    private Price $price;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getSchedule(): string
    {
        return $this->schedule;
    }

    public function setSchedule(string $schedule): void
    {
        $this->schedule = $schedule;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(?\DateTimeInterface $endedAt): void
    {
        $this->endedAt = $endedAt;
    }

    public function getValidUntil(): \DateTimeInterface
    {
        return $this->validUntil;
    }

    public function setValidUntil(\DateTimeInterface $validUntil): void
    {
        $this->validUntil = $validUntil;
    }

    public function getExternalMainReference(): string
    {
        return $this->externalMainReference;
    }

    public function setExternalMainReference(string $externalMainReference): void
    {
        $this->externalMainReference = $externalMainReference;
    }

    public function getExternalMainReferenceDetailsUrl(): ?string
    {
        return $this->externalMainReferenceDetailsUrl;
    }

    public function setExternalMainReferenceDetailsUrl(?string $externalMainReferenceDetailsUrl): void
    {
        $this->externalMainReferenceDetailsUrl = $externalMainReferenceDetailsUrl;
    }

    public function getChildExternalReference(): string
    {
        return $this->childExternalReference;
    }

    public function setChildExternalReference(string $childExternalReference): void
    {
        $this->childExternalReference = $childExternalReference;
    }

    public function getSubscriptionPlan(): SubscriptionPlan
    {
        return $this->subscriptionPlan;
    }

    public function setSubscriptionPlan(SubscriptionPlan $subscriptionPlan): void
    {
        $this->subscriptionPlan = $subscriptionPlan;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function setPrice(Price $price): void
    {
        $this->price = $price;
    }
}
