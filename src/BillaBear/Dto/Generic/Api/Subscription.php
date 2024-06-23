<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Api;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Subscription
{
    private string $id;

    #[SerializedName('schedule')]
    private ?string $schedule;

    #[SerializedName('created_at')]
    private \DateTimeInterface $createdAt;

    #[SerializedName('updated_at')]
    private \DateTimeInterface $updatedAt;

    #[SerializedName('ended_at')]
    private ?\DateTimeInterface $endedAt = null;

    #[SerializedName('valid_until')]
    private \DateTimeInterface $validUntil;

    #[SerializedName('main_external_reference')]
    private ?string $mainExternalReference;

    #[SerializedName('child_external_reference')]
    private ?string $childExternalReference;

    #[SerializedName('plan')]
    private SubscriptionPlan $plan;

    #[SerializedName('price')]
    private Price $price;

    #[SerializedName('seat_number')]
    private ?int $seatNumber = null;

    private string $status;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    public function setSchedule(?string $schedule): void
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

    public function getMainExternalReference(): ?string
    {
        return $this->mainExternalReference;
    }

    public function setMainExternalReference(?string $mainExternalReference): void
    {
        $this->mainExternalReference = $mainExternalReference;
    }

    public function getChildExternalReference(): ?string
    {
        return $this->childExternalReference;
    }

    public function setChildExternalReference(?string $childExternalReference): void
    {
        $this->childExternalReference = $childExternalReference;
    }

    public function getPlan(): SubscriptionPlan
    {
        return $this->plan;
    }

    public function setPlan(SubscriptionPlan $plan): void
    {
        $this->plan = $plan;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function setPrice(Price $price): void
    {
        $this->price = $price;
    }

    public function getSeatNumber(): ?int
    {
        return $this->seatNumber;
    }

    public function setSeatNumber(?int $seatNumber): void
    {
        $this->seatNumber = $seatNumber;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
