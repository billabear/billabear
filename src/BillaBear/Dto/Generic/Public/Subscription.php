<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Public;

use Parthenon\Billing\Enum\SubscriptionStatus;
use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class Subscription
{
    public function __construct(
        public string $id,
        public ?SubscriptionPlan $plan,
        #[SerializedName('price')]
        public ?Price $price,
        #[SerializedName('schedule')]
        public ?string $schedule,
        public SubscriptionStatus $status,
        #[SerializedName('created_at')]
        public \DateTimeInterface $createdAt,
        #[SerializedName('valid_until')]
        public \DateTimeInterface $validUntil,
    ) {
    }
}
