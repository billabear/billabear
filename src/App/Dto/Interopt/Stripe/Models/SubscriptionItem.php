<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Interopt\Stripe\Models;

use Symfony\Component\Serializer\Annotation\SerializedName;

class SubscriptionItem
{
    private string $id;

    private string $object = 'subscription_item';

    private ?array $metadata = null;

    private ?Price $price = null;

    private ?int $quantity = null;

    private ?string $subscription = null;

    #[SerializedName('billing_threshold')]
    private ?array $billingThresholds = null;

    private int $created;

    #[SerializedName('tax_rates')]
    private ?array $taxRates = null;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function setObject(string $object): void
    {
        $this->object = $object;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function setPrice(?Price $price): void
    {
        $this->price = $price;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getSubscription(): ?string
    {
        return $this->subscription;
    }

    public function setSubscription(?string $subscription): void
    {
        $this->subscription = $subscription;
    }

    public function getBillingThresholds(): ?array
    {
        return $this->billingThresholds;
    }

    public function setBillingThresholds(?array $billingThresholds): void
    {
        $this->billingThresholds = $billingThresholds;
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function setCreated(int $created): void
    {
        $this->created = $created;
    }

    public function getTaxRates(): ?array
    {
        return $this->taxRates;
    }

    public function setTaxRates(?array $taxRates): void
    {
        $this->taxRates = $taxRates;
    }
}
