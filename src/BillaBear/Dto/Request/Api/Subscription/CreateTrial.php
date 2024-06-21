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
    #[SubscriptionPlanExists]
    #[SubscriptionPlanHasStandaloneTrial]
    #[SerializedName('subscription_plan')]
    private $subscription_plan;

    #[Assert\Type('integer')]
    #[Assert\Positive]
    private $trial_length;

    #[Assert\Type('integer')]
    #[Assert\Positive]
    #[SerializedName('seat_number')]
    private $seat_number;

    public function getSubscriptionPlan()
    {
        return $this->subscription_plan;
    }

    public function setSubscriptionPlan($subscription_plan): void
    {
        $this->subscription_plan = $subscription_plan;
    }

    public function getTrialLength()
    {
        return $this->trial_length;
    }

    public function setTrialLength($trial_length): void
    {
        $this->trial_length = $trial_length;
    }

    public function getSeatNumber()
    {
        return $this->seat_number;
    }

    public function setSeatNumber(int $seat_number): void
    {
        $this->seat_number = $seat_number;
    }
}
