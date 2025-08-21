<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Api;

use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class Refund
{
    public function __construct(
        public string $id,
        public int $amount,
        public string $currency,
        public Customer $customer,
        public Payment $payment,
        #[SerializedName('billing_admin')]
        public ?BillingAdmin $billingAdmin = null,
        public string $status,
        public ?string $comment = null,
        #[SerializedName('created_at')]
        public \DateTimeInterface $createdAt,
    ) {
    }
}
