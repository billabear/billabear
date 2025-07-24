<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Api\Subscription;

use BillaBear\Validator\Constraints\PaymentMethodExists;
use BillaBear\Validator\Constraints\PriceExists;
use BillaBear\Validator\Constraints\SubscriptionPlanExists;
use BillaBear\Validator\Constraints\ValidPrice;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ValidPrice]
readonly class CreateSubscription
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[SerializedName('subscription_plan')]
        #[SubscriptionPlanExists]
        public ?string $subscription_plan = null,

        #[Assert\Type('string')]
        #[PriceExists]
        #[SerializedName('price')]
        public ?string $price = null,

        #[Assert\Type('string')]
        public ?string $currency = null,

        #[Assert\Type('string')]
        public ?string $card_token = null,

        #[Assert\Choice(choices: ['week', 'month', 'year'])]
        #[Assert\Type('string')]
        public ?string $schedule = null,

        #[PaymentMethodExists]
        #[SerializedName('payment_details')]
        public ?string $payment_details = null,

        #[Assert\Positive]
        #[Assert\Type('integer')]
        #[SerializedName('seat_number')]
        public int $seat_number = 1,

        #[Assert\Type('boolean')]
        public ?bool $deny_trial = null,

        #[Assert\Type('array')]
        public ?array $metadata = null,
    ) {
    }
}
