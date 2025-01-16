<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Public;

use BillaBear\Dto\Generic\App\Price;
use BillaBear\Dto\Generic\App\SubscriptionPlan;
use Symfony\Component\Serializer\Annotation\SerializedName;

class CheckoutLine
{
    #[SerializedName('subscription_plan')]
    private ?SubscriptionPlan $subscriptionPlan = null;

    private ?Price $price = null;

    private ?string $description = null;

    private string $currency;

    private ?int $total = null;

    #[SerializedName('seat_number')]
    private ?int $seatNumber = null;

    #[SerializedName('sub_total')]
    private ?int $subTotal = null;

    #[SerializedName('tax_total')]
    private ?int $taxTotal = null;

    #[SerializedName('tax_rate')]
    private ?float $taxRate;

    private ?string $schedule;

    public function getSubscriptionPlan(): ?SubscriptionPlan
    {
        return $this->subscriptionPlan;
    }

    public function setSubscriptionPlan(?SubscriptionPlan $subscriptionPlan): void
    {
        $this->subscriptionPlan = $subscriptionPlan;
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function setPrice(?Price $price): void
    {
        $this->price = $price;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(?int $total): void
    {
        $this->total = $total;
    }

    public function getSubTotal(): ?int
    {
        return $this->subTotal;
    }

    public function setSubTotal(?int $subTotal): void
    {
        $this->subTotal = $subTotal;
    }

    public function getTaxTotal(): ?int
    {
        return $this->taxTotal;
    }

    public function setTaxTotal(?int $taxTotal): void
    {
        $this->taxTotal = $taxTotal;
    }

    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    public function setTaxRate(?float $taxRate): void
    {
        $this->taxRate = $taxRate;
    }

    public function getSeatNumber(): ?int
    {
        return $this->seatNumber;
    }

    public function setSeatNumber(?int $seatNumber): void
    {
        $this->seatNumber = $seatNumber;
    }

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    public function setSchedule(?string $schedule): void
    {
        $this->schedule = $schedule;
    }
}
