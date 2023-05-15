<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Entity;

use App\Enum\CustomerStatus;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Common\Address;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'customers')]
class Customer implements CustomerInterface
{
    public const DEFAULT_BRAND = 'default';
    public const DEFAULT_LOCALE = 'en';

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

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $name = null;

    #[ORM\Column(name: 'brand', type: 'string', nullable: false)]
    private string $brand = self::DEFAULT_BRAND;

    #[ORM\ManyToOne(targetEntity: BrandSettings::class)]
    private BrandSettings $brandSettings;

    #[ORM\Column(name: 'locale', type: 'string', nullable: false)]
    private string $locale = self::DEFAULT_LOCALE;

    #[ORM\Column(name: 'payment_provider_details_url', type: 'string', nullable: true)]
    protected ?string $paymentProviderDetailsUrl;

    #[ORM\Column(name: 'status', type: 'string', nullable: true, enumType: CustomerStatus::class)]
    protected CustomerStatus $status;

    #[ORM\Column(type: 'boolean', nullable: true)]
    protected ?bool $disabled = false;

    #[Orm\OneToMany(targetEntity: Subscription::class, mappedBy: 'customer')]
    protected Collection $subscriptions;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
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
            throw new \Exception('No billing address');
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
}
