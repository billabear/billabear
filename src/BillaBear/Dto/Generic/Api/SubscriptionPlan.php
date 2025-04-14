<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Api;

use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class SubscriptionPlan
{
    public function __construct(
        public string $id,
        public string $name,
        #[SerializedName('code_name')]
        public ?string $codeName,
        #[SerializedName('user_count')]
        public int $userCount,
        #[SerializedName('per_seat')]
        public bool $perSeat,
        #[SerializedName('has_trial')]
        public ?bool $hasTrial,
        #[SerializedName('trial_length_days')]
        public ?int $trialLengthDays,
        public bool $free,
        public bool $public,
        public array $features,
        public array $prices,
        public array $limits,
        public Product $product,
        #[SerializedName('is_trial_standalone')]
        public bool $isTrialStandalone,
    ) {
    }
}
