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

namespace App\Dto\Generic\App;

use App\Dto\Generic\Address;
use Symfony\Component\Serializer\Annotation\SerializedName;

class Customer
{
    #[SerializedName('id')]
    protected string $id;

    #[SerializedName('name')]
    protected ?string $name = null;

    #[SerializedName('email')]
    protected ?string $email = null;

    #[SerializedName('reference')]
    protected ?string $reference = null;

    #[SerializedName('external_reference')]
    protected string $externalReference;

    #[SerializedName('payment_provider_details_url')]
    protected ?string $paymentProviderDetailsUrl = null;

    #[SerializedName('address')]
    protected Address $address;

    #[SerializedName('status')]
    protected string $status;

    #[SerializedName('brand')]
    protected string $brand;

    #[SerializedName('locale')]
    protected string $locale;

    #[SerializedName('billing_type')]
    protected string $billingType;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }

    public function getExternalReference(): string
    {
        return $this->externalReference;
    }

    public function setExternalReference(string $externalReference): void
    {
        $this->externalReference = $externalReference;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }

    public function getPaymentProviderDetailsUrl(): ?string
    {
        return $this->paymentProviderDetailsUrl;
    }

    public function setPaymentProviderDetailsUrl(?string $paymentProviderDetailsUrl): void
    {
        $this->paymentProviderDetailsUrl = $paymentProviderDetailsUrl;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
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

    public function getBillingType(): string
    {
        return $this->billingType;
    }

    public function setBillingType(string $billingType): void
    {
        $this->billingType = $billingType;
    }
}
