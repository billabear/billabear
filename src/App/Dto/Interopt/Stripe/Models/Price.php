<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Interopt\Stripe\Models;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Price
{
    private string $id;

    private string $object = 'price';

    private bool $active;

    private string $currency;

    private ?array $metadata = null;

    private ?string $nickname;

    private string $product;

    private ?array $recurring = null;

    private string $type;

    private int $unitAmount;

    #[SerializedName('billing_scheme')]
    private ?string $billingScheme = null;

    private int $created;

    #[SerializedName('currency_options')]
    private ?array $currencyOptions = null;

    #[SerializedName('custom_unit_amount')]
    private ?array $customUnitAmount = null;

    private bool $livemode;

    #[SerializedName('lookup_key')]
    private string $lookupKey;

    #[SerializedName('tax_behavior')]
    private ?string $taxBehavior = null;

    private ?array $tiers = [];

    #[SerializedName('transform_quantity')]
    private ?array $transformQuantity = null;

    #[SerializedName('unit_amount_decimal')]
    private string $unitAmountDecimal;

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

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = strtolower($currency);
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function getProduct(): string
    {
        return $this->product;
    }

    public function setProduct(string $product): void
    {
        $this->product = $product;
    }

    public function getRecurring(): ?array
    {
        return $this->recurring;
    }

    public function setRecurring(?array $recurring): void
    {
        $this->recurring = $recurring;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getUnitAmount(): int
    {
        return $this->unitAmount;
    }

    public function setUnitAmount(int $unitAmount): void
    {
        $this->unitAmount = $unitAmount;
    }

    public function getBillingScheme(): ?string
    {
        return $this->billingScheme;
    }

    public function setBillingScheme(?string $billingScheme): void
    {
        $this->billingScheme = $billingScheme;
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function setCreated(int $created): void
    {
        $this->created = $created;
    }

    public function getCurrencyOptions(): ?array
    {
        return $this->currencyOptions;
    }

    public function setCurrencyOptions(?array $currencyOptions): void
    {
        $this->currencyOptions = $currencyOptions;
    }

    public function getCustomUnitAmount(): ?array
    {
        return $this->customUnitAmount;
    }

    public function setCustomUnitAmount(?array $customUnitAmount): void
    {
        $this->customUnitAmount = $customUnitAmount;
    }

    public function isLivemode(): bool
    {
        return $this->livemode;
    }

    public function setLivemode(bool $livemode): void
    {
        $this->livemode = $livemode;
    }

    public function getLookupKey(): string
    {
        return $this->lookupKey;
    }

    public function setLookupKey(string $lookupKey): void
    {
        $this->lookupKey = $lookupKey;
    }

    public function getTaxBehavior(): ?string
    {
        return $this->taxBehavior;
    }

    public function setTaxBehavior(?string $taxBehavior): void
    {
        $this->taxBehavior = $taxBehavior;
    }

    public function getTiers(): ?array
    {
        return $this->tiers;
    }

    public function setTiers(?array $tiers): void
    {
        $this->tiers = $tiers;
    }

    public function getTransformQuantity(): ?array
    {
        return $this->transformQuantity;
    }

    public function setTransformQuantity(?array $transformQuantity): void
    {
        $this->transformQuantity = $transformQuantity;
    }

    public function getUnitAmountDecimal(): string
    {
        return $this->unitAmountDecimal;
    }

    public function setUnitAmountDecimal(string $unitAmountDecimal): void
    {
        $this->unitAmountDecimal = $unitAmountDecimal;
    }
}
