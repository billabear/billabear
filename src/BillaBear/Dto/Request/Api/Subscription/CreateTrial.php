<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Api\Subscription;

use BillaBear\Validator\Constraints\SubscriptionPlanExists;
use BillaBear\Validator\Constraints\SubscriptionPlanHasStandaloneTrial;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTrial
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[SerializedName('subscription_plan')]
    #[SubscriptionPlanExists]
    #[SubscriptionPlanHasStandaloneTrial]
    private $subscription_plan;

    #[Assert\Positive]
    #[Assert\Type('integer')]
    private $trial_length_days;

    #[Assert\Positive]
    #[Assert\Type('integer')]
    #[SerializedName('seat_number')]
    private $seat_number;

    #[Assert\Type('string')]
    private $card_token;

    public function getSubscriptionPlan()
    {
        return $this->subscription_plan;
    }

    public function setSubscriptionPlan($subscription_plan): void
    {
        $this->subscription_plan = $subscription_plan;
    }

    public function getTrialLengthDays()
    {
        return $this->trial_length_days;
    }

    public function setTrialLengthDays($trial_length): void
    {
        $this->trial_length_days = $trial_length;
    }

    public function getSeatNumber()
    {
        return $this->seat_number;
    }

    public function setSeatNumber(int $seat_number): void
    {
        $this->seat_number = $seat_number;
    }

    public function getCardToken()
    {
        return $this->card_token;
    }

    public function setCardToken($card_token): void
    {
        $this->card_token = $card_token;
    }
}
