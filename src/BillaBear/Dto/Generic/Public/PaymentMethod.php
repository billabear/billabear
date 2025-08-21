<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Public;

use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class PaymentMethod
{
    public function __construct(
        #[SerializedName('id')]
        public string $id,
        #[SerializedName('name')]
        public ?string $name,
        #[SerializedName('default')]
        public bool $default,
        #[SerializedName('brand')]
        public ?string $brand,
        #[SerializedName('last_four')]
        public ?string $lastFour,
        #[SerializedName('expiry_month')]
        public ?string $expiryMonth,
        #[SerializedName('expiry_year')]
        public ?string $expiryYear,
        #[SerializedName('created_at')]
        public \DateTimeInterface $createdAt,
    ) {
    }
}
