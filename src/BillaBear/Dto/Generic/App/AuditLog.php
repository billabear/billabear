<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\App;

use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class AuditLog
{
    public function __construct(
        public string $message,
        public string $type,
        #[SerializedName('created_at')]
        public \DateTime $createdAt,
        public array $context = [],
        #[SerializedName('billing_admin')]
        public ?BillingAdmin $billingAdmin = null,
    ) {
    }
}
