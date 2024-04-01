<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Entity;

use App\Enum\CustomerStatus;
use App\Enum\CustomerType;
use Brick\Money\Money;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Common\Address;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'customers')]
#[ORM\Index(name: 'email_idx', fields: ['billingEmail'])]
#[ORM\Index(name: 'external_ref_idx', fields: ['externalCustomerReference'])]
class Customer implements CustomerInterface
{
    public const BILLING_TYPE_CARD = 'card';
    public const BILLING_TYPE_INVOICE = 'invoice';

    public const DEFAULT_BRAND = 'default';
    public const DEFAULT_LOCALE = 'en';
    public const DEFAULT_BILLING_TYPE = self::BILLING_TYPE_CARD;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\Embedded(class: Address::class)]
    private ?Address $billingAddress;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $reference;

    #[ORM\Column(type: 'string')]
    private string $externalCustomerReference;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $billingEmail;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $billingType = self::DEFAULT_BILLING_TYPE;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $name = null;

    #[ORM\Column(name: 'brand', type: 'string', nullable: false)]
    private string $brand = self::DEFAULT_BRAND;

    #[ORM\ManyToOne(targetEntity: BrandSettings::class)]
    private BrandSettings $brandSettings;

    #[ORM\Column(name: 'locale', type: 'string', nullable: false)]
    private string $locale = self::DEFAULT_LOCALE;

    #[ORM\Column(name: 'payment_provider_details_url', type: 'string', nullable: true)]
    protected ?string $paymentProviderDetailsUrl = null;

    #[ORM\Column(name: 'status', type: 'string', nullable: true, enumType: CustomerStatus::class)]
    protected CustomerStatus $status;

    #[ORM\Column(type: 'boolean', nullable: true)]
    protected ?bool $disabled = false;

    #[ORM\OneToMany(targetEntity: Subscription::class, mappedBy: 'customer')]
    protected Collection $subscriptions;

    #[ORM\Column(name: 'credit_amount', type: 'integer', nullable: true)]
    protected ?int $creditAmount = null;

    #[ORM\Column(name: 'credit_currency', type: 'string', nullable: true)]
    protected ?string $creditCurrency = null;

    #[ORM\Column('created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(name: 'tax_number', type: 'string', nullable: true)]
    protected ?string $taxNumber = null;

    #[ORM\Column(name: 'tax_exempt', type: 'boolean', nullable: true)]
    protected ?bool $taxExempt = false;

    #[ORM\Column(name: 'tax_rate_digital', type: 'float', nullable: true)]
    protected ?float $digitalTaxRate = 0.0;

    #[ORM\Column(name: 'tax_rate_standard', type: 'float', nullable: true)]
    protected ?float $standardTaxRate = 0.0;

    #[ORM\Column(enumType: CustomerType::class)]
    protected CustomerType $type;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }

    public function getExternalCustomerReference(): string
    {
        return $this->externalCustomerReference;
    }

    /**
     * @param string $externalCustomerReference
     */
    public function setExternalCustomerReference($externalCustomerReference)
    {
        $this->externalCustomerReference = $externalCustomerReference;
    }

    public function hasExternalsCustomerReference(): bool
    {
        return isset($this->externalCustomerReference);
    }

    public function getBillingEmail(): ?string
    {
        return $this->billingEmail;
    }

    public function setBillingEmail(?string $billingEmail): void
    {
        $this->billingEmail = $billingEmail;
    }

    public function setBillingAddress(Address $address)
    {
        $this->billingAddress = $address;
    }

    public function getBillingAddress(): Address
    {
        if (!isset($this->billingAddress)) {
            return new Address();
        }

        return $this->billingAddress;
    }

    public function hasBillingAddress(): bool
    {
        return isset($this->billingAddress);
    }

    public function getCountry(): string
    {
        return $this->billingAddress->getCountry();
    }

    public function getDisplayName(): string
    {
        return $this->name ?? $this->billingEmail;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function setPaymentProviderDetailsUrl(?string $paymentProviderDetailsUrl): void
    {
        $this->paymentProviderDetailsUrl = $paymentProviderDetailsUrl;
    }

    public function getPaymentProviderDetailsUrl()
    {
        return $this->paymentProviderDetailsUrl;
    }

    public function hasExternalCustomerReference(): bool
    {
        return isset($this->externalCustomerReference);
    }

    public function getStatus(): CustomerStatus
    {
        if (!isset($this->status)) {
            return CustomerStatus::UNKNOWN;
        }

        return $this->status;
    }

    public function setStatus(CustomerStatus $status): void
    {
        if (CustomerStatus::DISABLED === $status) {
            $this->disabled = true;
        } else {
            $this->disabled = false;
        }

        $this->status = $status;
    }

    public function isDisabled(): bool
    {
        return true == $this->disabled;
    }

    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getBrandSettings(): BrandSettings
    {
        return $this->brandSettings;
    }

    public function setBrandSettings(BrandSettings $brandSettings): void
    {
        $this->brandSettings = $brandSettings;
    }

    /**
     * @return Collection|Subscription[]
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function setSubscriptions(Collection $subscriptions): void
    {
        $this->subscriptions = $subscriptions;
    }

    public function getBillingType(): string
    {
        return $this->billingType;
    }

    public function setBillingType(string $billingType): void
    {
        $this->billingType = $billingType;
    }

    public function getCreditAmount(): ?int
    {
        return $this->creditAmount;
    }

    public function setCreditAmount(?int $creditAmount): void
    {
        $this->creditAmount = $creditAmount;
    }

    public function getCreditCurrency(): ?string
    {
        return $this->creditCurrency;
    }

    public function setCreditCurrency(?string $creditCurrency): void
    {
        $this->creditCurrency = $creditCurrency;
    }

    public function hasCredit(): bool
    {
        return isset($this->creditCurrency) && isset($this->creditAmount);
    }

    public function getCreditAsMoney(): Money
    {
        if (null === $this->creditCurrency) {
            throw new \Exception('No currency');
        }

        if (null === $this->creditAmount) {
            return Money::zero($this->creditCurrency);
        }

        return Money::ofMinor($this->creditAmount, $this->creditCurrency);
    }

    public function addCreditAsMoney(Money $money): void
    {
        if (null === $this->creditCurrency) {
            $this->creditCurrency = $money->getCurrency()->getCurrencyCode();
        }

        $newAmount = $this->getCreditAsMoney()->plus($money);
        $this->creditAmount = $newAmount->getMinorAmount()->toInt();
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getTaxNumber(): ?string
    {
        return $this->taxNumber;
    }

    public function setTaxNumber(?string $taxNumber): void
    {
        $this->taxNumber = $taxNumber;
    }

    public function getTaxExempt(): ?bool
    {
        return $this->taxExempt;
    }

    public function setTaxExempt(?bool $taxExempt): void
    {
        $this->taxExempt = $taxExempt;
    }

    public function getDigitalTaxRate(): ?float
    {
        return $this->digitalTaxRate;
    }

    public function setDigitalTaxRate(?float $digitalTaxRate): void
    {
        $this->digitalTaxRate = $digitalTaxRate;
    }

    public function getStandardTaxRate(): ?float
    {
        return $this->standardTaxRate;
    }

    public function setStandardTaxRate(?float $standardTaxRate): void
    {
        $this->standardTaxRate = $standardTaxRate;
    }

    public function getType(): CustomerType
    {
        return $this->type;
    }

    public function setType(CustomerType $type): void
    {
        $this->type = $type;
    }
}
