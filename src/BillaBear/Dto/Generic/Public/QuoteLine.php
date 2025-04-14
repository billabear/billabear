<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Public;

use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class QuoteLine
{
    public function __construct(
        #[SerializedName('subscription_plan')]
        public ?SubscriptionPlan $subscriptionPlan,
        public ?Price $price,
        public ?string $description,
        public string $currency,
        public int $total,
        #[SerializedName('seat_number')]
        public ?int $seatNumber,
        #[SerializedName('sub_total')]
        public int $subTotal,
        #[SerializedName('tax_total')]
        public int $taxTotal,
        #[SerializedName('tax_rate')]
        public ?float $taxRate,
    ) {
    }
}
