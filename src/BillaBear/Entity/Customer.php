<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use BillaBear\Customer\CustomerStatus;
use BillaBear\Customer\CustomerType;
use BillaBear\Logger\Audit\AuditableInterface;
use BillaBear\Pricing\Usage\WarningLevel;
use Brick\Money\Money;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Common\Address;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Index(name: 'email_idx', fields: ['billingEmail'])]
#[ORM\Index(name: 'external_ref_idx', fields: ['externalCustomerReference'])]
#[ORM\Table(name: 'customers')]
class Customer implements CustomerInterface, AuditableInterface
{
    public const BILLING_TYPE_CARD = 'card';
    public const BILLING_TYPE_INVOICE = 'invoice';

    public const DEFAULT_BRAND = 'default';
    public const DEFAULT_LOCALE = 'en';
    public const DEFAULT_BILLING_TYPE = self::BILLING_TYPE_CARD;

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

    #[ORM\Column(name: 'tax_number', type: 'string', nullable: true)]
    protected ?string $taxNumber = null;

    #[ORM\Column(name: 'tax_exempt', type: 'boolean', nullable: true)]
    protected ?bool $taxExempt = false;

    #[ORM\Column(name: 'tax_rate_standard', type: 'float', nullable: true)]
    protected ?float $standardTaxRate = null;

    #[ORM\Column(enumType: CustomerType::class)]
    protected CustomerType $type;

    #[ORM\Column(name: 'invoice_format', type: 'string', nullable: true)]
    protected ?string $invoiceFormat = null;

    #[ORM\Column(name: 'warning_level', type: 'integer', enumType: WarningLevel::class, nullable: true)]
    protected ?WarningLevel $warningLevel = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
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

    #[ORM\Column('created_at', type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $accountingReference;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $customerSupportReference;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $marketingOptIn = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $newsletterMarketingReference;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $newsletterAnnouncementReference;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $crmReference = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $crmContactReference = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $metadata = [];

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

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
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

    public function getStandardTaxRate(): ?float
    {
        return $this->standardTaxRate;
    }

    public function setStandardTaxRate(?float $standardTaxRate): void
    {
        $this->standardTaxRate = $standardTaxRate;
    }

    public function hasStandardTaxRate(): bool
    {
        return isset($this->standardTaxRate);
    }

    public function getType(): CustomerType
    {
        return $this->type;
    }

    public function setType(CustomerType $type): void
    {
        $this->type = $type;
    }

    public function isBusiness(): bool
    {
        return CustomerType::BUSINESS === $this->type;
    }

    public function setEnabled(bool $enabled)
    {
        $this->disabled = !$enabled;
    }

    public function isEnabled(): bool
    {
        return !$this->disabled;
    }

    public function getInvoiceFormat(): ?string
    {
        return $this->invoiceFormat;
    }

    public function setInvoiceFormat(?string $invoiceFormat): void
    {
        $this->invoiceFormat = $invoiceFormat;
    }

    public function getWarningLevel(): WarningLevel
    {
        if (!isset($this->warningLevel)) {
            return WarningLevel::NO_WARNING;
        }

        return $this->warningLevel;
    }

    public function setWarningLevel(?WarningLevel $warningLevel): void
    {
        $this->warningLevel = $warningLevel;
    }

    public function getAccountingReference(): ?string
    {
        return $this->accountingReference;
    }

    public function setAccountingReference(?string $accountingReference): void
    {
        $this->accountingReference = $accountingReference;
    }

    public function getCustomerSupportReference(): ?string
    {
        return $this->customerSupportReference;
    }

    public function setCustomerSupportReference(?string $customerSupportReference): void
    {
        $this->customerSupportReference = $customerSupportReference;
    }

    public function getMarketingOptIn(): bool
    {
        if (!isset($this->marketingOptIn)) {
            // If they've not opted in they've not opted in.
            return false;
        }

        return $this->marketingOptIn;
    }

    public function setMarketingOptIn(?bool $marketingOptIn): void
    {
        $this->marketingOptIn = $marketingOptIn;
    }

    public function getNewsletterMarketingReference(): ?string
    {
        return $this->newsletterMarketingReference;
    }

    public function setNewsletterMarketingReference(?string $newsletterMarketingReference): void
    {
        $this->newsletterMarketingReference = $newsletterMarketingReference;
    }

    public function getNewsletterAnnouncementReference(): ?string
    {
        return $this->newsletterAnnouncementReference;
    }

    public function setNewsletterAnnouncementReference(?string $newsletterAnnouncementReference): void
    {
        $this->newsletterAnnouncementReference = $newsletterAnnouncementReference;
    }

    public function getCrmReference(): ?string
    {
        return $this->crmReference;
    }

    public function setCrmReference(?string $crmReference): void
    {
        $this->crmReference = $crmReference;
    }

    public function getCrmContactReference(): ?string
    {
        return $this->crmContactReference;
    }

    public function setCrmContactReference(?string $crmContactReference): void
    {
        $this->crmContactReference = $crmContactReference;
    }

    public function getMetadata(): array
    {
        if (!isset($this->metadata)) {
            return [];
        }

        return $this->metadata;
    }

    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function getAuditName(): string
    {
        return 'Customer';
    }

    public function getAuditLogIdTag(): string
    {
        return 'customer_id';
    }
}
