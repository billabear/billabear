<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Public;

use BillaBear\Dto\Generic\Address;
use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class Invoice
{
    public function __construct(
        public string $id,
        public string $number,
        public string $currency,
        public int $amount,
        public bool $paid,
        #[SerializedName('created_at')]
        public \DateTime $createdAt,
        #[SerializedName('biller_address')]
        public ?Address $billerAddress,
        #[SerializedName('payee_address')]
        public ?Address $payeeAddress,
        #[SerializedName('email_address')]
        public string $emailAddress,
        public array $lines,
        #[SerializedName('paid_at')]
        public ?\DateTime $paidAt,
    ) {
    }
}
