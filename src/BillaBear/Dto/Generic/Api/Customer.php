<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Api;

use BillaBear\Dto\Generic\Address;
use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class Customer
{
    public function __construct(
        #[SerializedName('id')]
        public string $id,
        #[SerializedName('name')]
        public ?string $name,
        #[SerializedName('email')]
        public string $email,
        #[SerializedName('reference')]
        public ?string $reference,
        #[SerializedName('external_reference')]
        public string $externalReference,
        #[SerializedName('address')]
        public Address $address,
        #[SerializedName('status')]
        public string $status,
        #[SerializedName('brand')]
        public string $brand,
        #[SerializedName('locale')]
        public string $locale,
        #[SerializedName('billing_type')]
        public string $billingType,
        #[SerializedName('tax_number')]
        public ?string $taxNumber,
        #[SerializedName('standard_tax_rate')]
        public ?float $standardTaxRate,
        protected string $type,
        #[SerializedName('invoice_format')]
        public ?string $invoiceFormat,
        #[SerializedName('marketing_opt_in')]
        public bool $marketingOptIn,
        public array $metadata,
    ) {
    }
}
