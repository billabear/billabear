<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Public;

use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class Quote
{
    public function __construct(
        public string $id,
        public string $currency,
        public Customer $customer,
        public int $total,
        #[SerializedName('sub_total')]
        public int $subTotal,
        #[SerializedName('tax_total')]
        public int $taxTotal,
        public array $lines,
        #[SerializedName('created_at')]
        public \DateTimeInterface $createdAt,
        public bool $paid,
        #[SerializedName('expires_at')]
        public ?\DateTime $expiresAt,
        public bool $expired,
    ) {
    }
}
