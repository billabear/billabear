<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Public;

use BillaBear\Dto\Generic\Address;
use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class Customer
{
    public function __construct(
        #[SerializedName('id')]
        protected string $id,
        #[SerializedName('name')]
        public ?string $name,
        #[SerializedName('email')]
        public ?string $email,
        #[SerializedName('address')]
        public Address $address,
        #[SerializedName('brand')]
        public string $brand,
        #[SerializedName('locale')]
        public string $locale,
        public string $type,
        #[SerializedName('billing_type')]
        public string $billingType,
    ) {
    }
}
