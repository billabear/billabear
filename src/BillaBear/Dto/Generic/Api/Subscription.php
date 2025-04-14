<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Api;

use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class Subscription
{
    public function __construct(
        public string $id,
        #[SerializedName('schedule')]
        public ?string $schedule,
        #[SerializedName('created_at')]
        public \DateTimeInterface $createdAt,
        #[SerializedName('updated_at')]
        public \DateTimeInterface $updatedAt,
        #[SerializedName('ended_at')]
        public ?\DateTimeInterface $endedAt,
        #[SerializedName('valid_until')]
        public \DateTimeInterface $validUntil,
        #[SerializedName('main_external_reference')]
        public ?string $mainExternalReference,
        #[SerializedName('child_external_reference')]
        public ?string $childExternalReference,
        #[SerializedName('plan')]
        public ?SubscriptionPlan $plan,
        #[SerializedName('price')]
        public ?Price $price,
        #[SerializedName('seat_number')]
        public ?int $seatNumber,
        public array $metadata,
        public string $status,
    ) {
    }
}
