<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Generic\Api;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Subscription
{
    private string $id;

    #[SerializedName('schedule')]
    private string $schedule;

    #[SerializedName('created_at')]
    private \DateTimeInterface $createdAt;

    #[SerializedName('updated_at')]
    private \DateTimeInterface $updatedAt;

    #[SerializedName('ended_at')]
    private ?\DateTimeInterface $endedAt = null;

    #[SerializedName('valid_until')]
    private \DateTimeInterface $validUntil;

    #[SerializedName('main_external_reference')]
    private string $mainExternalReference;

    #[SerializedName('child_external_reference')]
    private string $childExternalReference;

    #[SerializedName('subscription_plan')]
    private SubscriptionPlan $subscriptionPlan;

    #[SerializedName('price')]
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

    public function getMainExternalReference(): string
    {
        return $this->mainExternalReference;
    }

    public function setMainExternalReference(string $mainExternalReference): void
    {
        $this->mainExternalReference = $mainExternalReference;
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
