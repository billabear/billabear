<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Api;

use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class PaymentMethod
{
    public function __construct(
        #[SerializedName('id')]
        public string $id,
        #[SerializedName('name')]
        public ?string $name = null,
        #[SerializedName('default')]
        public bool $default = true,
        #[SerializedName('brand')]
        public ?string $brand = null,
        #[SerializedName('last_four')]
        public ?string $lastFour = null,
        #[SerializedName('expiry_month')]
        public ?string $expiryMonth = null,
        #[SerializedName('expiry_year')]
        public ?string $expiryYear = null,
        #[SerializedName('created_at')]
        public \DateTimeInterface $createdAt,
    ) {
    }
}
