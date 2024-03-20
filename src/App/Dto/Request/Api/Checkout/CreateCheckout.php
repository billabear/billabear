<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\Api\Checkout;

use App\Validator\Constraints\BrandCodeExists;
use App\Validator\Constraints\CustomerExists;
use App\Validator\Constraints\SamePaymentSchedule;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCheckout
{
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type('string')]
    private $name;

    #[Assert\NotBlank()]
    #[SerializedName('brand')]
    #[BrandCodeExists]
    private $brand;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type('string')]
    #[Assert\Regex('~[a-zA-Z0-9_-]+~isU')]
    private $slug;

    #[Assert\NotBlank()]
    #[Assert\Type('boolean')]
    private $permanent = false;

    #[CustomerExists]
    private $customer;

    #[Assert\Valid]
    private $items = [];

    #[Assert\Valid]
    #[SamePaymentSchedule]
    private $subscriptions = [];

    #[Assert\DateTime(format: DATE_ATOM)]
    #[SerializedName('expires_at')]
    private $expiresAt;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getBrand()
    {
        return $this->brand;
    }

    public function setBrand($brand): void
    {
        $this->brand = $brand;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    public function isPermanent(): bool
    {
        return $this->permanent;
    }

    public function setPermanent(bool $permanent): void
    {
        $this->permanent = $permanent;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setCustomer($customer): void
    {
        $this->customer = $customer;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function getSubscriptions(): array
    {
        return $this->subscriptions;
    }

    public function setSubscriptions(array $subscriptions): void
    {
        $this->subscriptions = $subscriptions;
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function setExpiresAt($expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    public function addItem(CreateCheckoutItem $createInvoiceItem): void
    {
        $this->items[] = $createInvoiceItem;
    }

    public function addSubscription(CreateCheckoutSubscription $createInvoiceSubscription): void
    {
        $this->subscriptions[] = $createInvoiceSubscription;
    }
}
