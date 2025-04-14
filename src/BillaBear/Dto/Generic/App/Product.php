<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\App;

use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class Product
{
    public function __construct(
        #[SerializedName('id')]
        public string $id,
        #[SerializedName('name')]
        public string $name,
        #[SerializedName('tax_type')]
        public ?TaxType $taxType,
        #[SerializedName('external_reference')]
        public ?string $externalReference,
        #[SerializedName('payment_provider_details_url')]
        public ?string $paymentProviderDetailsUrl,
        #[SerializedName('tax_rate')]
        public ?float $taxRate,
        public ?bool $physical,
    ) {
    }
}
