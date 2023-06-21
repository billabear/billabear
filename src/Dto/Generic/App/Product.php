<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
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

    #[SerializedName('external_reference')]
    protected ?string $externalReference = null;

    #[SerializedName('payment_provider_details_url')]
    protected ?string $paymentProviderDetailsUrl = null;

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

    /**
     * @return string
     */
    public function getExternalReference(): ?string
    {
        return $this->externalReference;
    }

    /**
     * @param string $externalReference
     */
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
}
