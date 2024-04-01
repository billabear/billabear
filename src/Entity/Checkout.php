<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Entity;

use Brick\Money\Money;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\BillingAdminInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
class Checkout
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private ?CustomerInterface $customer = null;

    #[ORM\Column(type: 'boolean')]
    private bool $permanent;

    #[ORM\ManyToOne(targetEntity: BrandSettings::class)]
    private BrandSettings $brandSettings;

    #[ORM\Column(type: 'string')]
    private string $slug;

    #[ORM\Column(type: 'string')]
    private string $currency;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $successRedirect = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $cancelRedirect = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $amountDue = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $total = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $subTotal = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $taxTotal = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $expiresAt = null;

    #[ORM\Column(type: 'boolean')]
    private bool $valid = true;

    #[ORM\OneToMany(targetEntity: CheckoutLine::class, mappedBy: 'checkout', cascade: ['persist'])]
    private array|Collection $lines;

    #[ORM\ManyToOne(targetEntity: BillingAdminInterface::class)]
    private ?BillingAdminInterface $createdBy = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updatedAt;

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
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCustomer(): ?CustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(?CustomerInterface $customer): void
    {
        $this->customer = $customer;
    }

    public function isPermanent(): bool
    {
        return $this->permanent;
    }

    public function setPermanent(bool $permanent): void
    {
        $this->permanent = $permanent;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getAmountDueAsMoney(): Money
    {
        return Money::ofMinor($this->amountDue, $this->currency);
    }

    public function getAmountDue(): ?int
    {
        return $this->amountDue;
    }

    public function setAmountDue(?int $amountDue): void
    {
        $this->amountDue = $amountDue;
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

    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTime $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): void
    {
        $this->valid = $valid;
    }

    /**
     * @return Collection|array|CheckoutLine[]
     */
    public function getLines(): Collection|array
    {
        return $this->lines;
    }

    public function setLines(Collection|array $lines): void
    {
        $this->lines = $lines;
    }

    public function getCreatedBy(): ?BillingAdminInterface
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?BillingAdminInterface $createdBy): void
    {
        $this->createdBy = $createdBy;
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

    public function getSuccessRedirect(): ?string
    {
        return $this->successRedirect;
    }

    public function setSuccessRedirect(?string $successRedirect): void
    {
        $this->successRedirect = $successRedirect;
    }

    public function getCancelRedirect(): ?string
    {
        return $this->cancelRedirect;
    }

    public function setCancelRedirect(?string $cancelRedirect): void
    {
        $this->cancelRedirect = $cancelRedirect;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getBrandSettings(): BrandSettings
    {
        return $this->brandSettings;
    }

    public function setBrandSettings(BrandSettings $brandSettings): void
    {
        $this->brandSettings = $brandSettings;
    }
}
