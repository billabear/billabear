<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App;

use BillaBear\Validator\Constraints\PaymentMethodExists;
use BillaBear\Validator\Constraints\PriceExists;
use BillaBear\Validator\Constraints\SubscriptionPlanExists;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

readonly class CreateSubscription
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[SerializedName('subscription_plan')]
        #[SubscriptionPlanExists]
        public string $subscriptionPlan,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[PriceExists]
        #[SerializedName('price')]
        public string $price,

        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Type('string')]
        #[PaymentMethodExists]
        #[SerializedName('payment_details')]
        public ?string $paymentDetails = null,

        #[Assert\Positive]
        #[Assert\Type('integer')]
        #[SerializedName('seat_number')]
        public int $seatNumber = 1,

        #[Assert\Type('boolean')]
        #[SerializedName('has_trial')]
        public ?bool $hasTrial = null,

        #[Assert\PositiveOrZero]
        #[Assert\Type('integer')]
        #[SerializedName('trial_length_days')]
        public ?int $trialLengthDays = null,
    ) {
    }
}
