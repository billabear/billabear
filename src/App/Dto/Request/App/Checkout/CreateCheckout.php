<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\App\Checkout;

use App\Validator\Constraints\BrandCodeExists;
use App\Validator\Constraints\CustomerExists;
use App\Validator\Constraints\SamePaymentSchedule;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCheckout
{
    #[Assert\NotBlank()]
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

    #[Assert\DateTime(format: DATE_RFC3339_EXTENDED)]
    #[SerializedName('expires_at')]
    private $expiresAt;

    public function isPermanent(): bool
    {
        return true === $this->permanent;
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

    public function getItems()
    {
        return $this->items;
    }

    public function setItems($items): void
    {
        $this->items = $items;
    }

    public function addItem(CreateCheckoutItem $createInvoiceItem): void
    {
        $this->items[] = $createInvoiceItem;
    }

    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    public function setSubscriptions($subscriptions): void
    {
        $this->subscriptions = $subscriptions;
    }

    public function addSubscription(CreateCheckoutSubscription $createInvoiceSubscription): void
    {
        $this->subscriptions[] = $createInvoiceSubscription;
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function setExpiresAt($expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    public function getBrand()
    {
        return $this->brand;
    }

    public function setBrand($brand): void
    {
        $this->brand = $brand;
    }
}