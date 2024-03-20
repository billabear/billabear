<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Generic\App;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Subscription
{
    private string $id;

    private string $schedule;

    private string $status;

    #[SerializedName('seat_number')]
    private ?int $seatNumber = null;

    #[SerializedName('created_at')]
    private \DateTimeInterface $createdAt;

    #[SerializedName('updated_at')]
    private \DateTimeInterface $updatedAt;

    #[SerializedName('ended_at')]
    private ?\DateTimeInterface $endedAt = null;

    #[SerializedName('valid_until')]
    private \DateTimeInterface $validUntil;

    #[SerializedName('main_external_reference')]
    private ?string $mainExternalReference = null;

    #[SerializedName('external_main_reference_details_url')]
    private ?string $paymentProviderDetailsUrl = null;

    #[SerializedName('child_external_reference')]
    private ?string $childExternalReference = null;

    #[SerializedName('plan')]
    private SubscriptionPlan $subscriptionPlan;

    private Price $price;

    private Customer $customer;

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

    public function getMainExternalReference(): ?string
    {
        return $this->mainExternalReference;
    }

    public function setMainExternalReference(?string $mainExternalReference): void
    {
        $this->mainExternalReference = $mainExternalReference;
    }

    public function getPaymentProviderDetailsUrl(): ?string
    {
        return $this->paymentProviderDetailsUrl;
    }

    public function setPaymentProviderDetailsUrl(?string $paymentProviderDetailsUrl): void
    {
        $this->paymentProviderDetailsUrl = $paymentProviderDetailsUrl;
    }

    public function getChildExternalReference(): ?string
    {
        return $this->childExternalReference;
    }

    public function setChildExternalReference(?string $childExternalReference): void
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getSeatNumber(): ?int
    {
        return $this->seatNumber;
    }

    public function setSeatNumber(?int $seatNumber): void
    {
        $this->seatNumber = $seatNumber;
    }
}
