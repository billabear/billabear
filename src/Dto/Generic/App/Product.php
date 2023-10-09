<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Generic\App;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Product
{
    #[SerializedName('id')]
    protected string $id;

    #[SerializedName('name')]
    protected string $name;

    #[SerializedName('tax_type')]
    protected string $taxType;

    #[SerializedName('external_reference')]
    protected ?string $externalReference = null;

    #[SerializedName('payment_provider_details_url')]
    protected ?string $paymentProviderDetailsUrl = null;

    #[SerializedName('tax_rate')]
    protected ?float $taxRate = null;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
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

    public function getExternalReference(): ?string
    {
        return $this->externalReference;
    }

    public function setExternalReference(?string $externalReference): void
    {
        $this->externalReference = $externalReference;
    }

    public function getPaymentProviderDetailsUrl(): ?string
    {
        return $this->paymentProviderDetailsUrl;
    }

    public function setPaymentProviderDetailsUrl(?string $paymentProviderDetailsUrl): void
    {
        $this->paymentProviderDetailsUrl = $paymentProviderDetailsUrl;
    }

    public function getTaxType(): string
    {
        return $this->taxType;
    }

    public function setTaxType(string $taxType): void
    {
        $this->taxType = $taxType;
    }

    public function getTaxRate(): ?float
    {
        return $this->taxRate;
    }

    public function setTaxRate(?float $taxRate): void
    {
        $this->taxRate = $taxRate;
    }
}
