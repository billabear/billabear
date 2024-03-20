<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Generic\Api;

use Symfony\Component\Serializer\Annotation\SerializedName;

class PaymentMethod
{
    #[SerializedName('id')]
    protected string $id;

    #[SerializedName('name')]
    protected ?string $name = null;

    #[SerializedName('default')]
    protected bool $default = true;

    #[SerializedName('brand')]
    protected ?string $brand = null;

    #[SerializedName('last_four')]
    protected ?string $lastFour = null;

    #[SerializedName('expiry_month')]
    protected ?string $expiryMonth = null;

    #[SerializedName('expiry_year')]
    protected ?string $expiryYear = null;

    #[SerializedName('created_at')]
    protected \DateTimeInterface $createdAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return (string) $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    public function setDefault(bool $default): void
    {
        $this->default = $default;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): void
    {
        $this->brand = $brand;
    }

    public function getLastFour(): ?string
    {
        return $this->lastFour;
    }

    public function setLastFour(?string $lastFour): void
    {
        $this->lastFour = $lastFour;
    }

    public function getExpiryMonth(): ?string
    {
        return $this->expiryMonth;
    }

    public function setExpiryMonth(?string $expiryMonth): void
    {
        $this->expiryMonth = $expiryMonth;
    }

    public function getExpiryYear(): ?string
    {
        return $this->expiryYear;
    }

    public function setExpiryYear(?string $expiryYear): void
    {
        $this->expiryYear = $expiryYear;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
