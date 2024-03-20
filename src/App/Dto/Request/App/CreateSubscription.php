<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\App;

use App\Validator\Constraints\PaymentMethodExists;
use App\Validator\Constraints\PriceExists;
use App\Validator\Constraints\SubscriptionPlanExists;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateSubscription
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[SubscriptionPlanExists]
    #[SerializedName('subscription_plan')]
    private $subscriptionPlan;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[PriceExists]
    #[SerializedName('price')]
    private $price;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type('string')]
    #[PaymentMethodExists]
    #[SerializedName('payment_details')]
    private $paymentDetails;

    #[Assert\Type('integer')]
    #[Assert\Positive]
    #[SerializedName('seat_number')]
    private $seatNumber = 1;

    #[SerializedName('has_trial')]
    #[Assert\Type('boolean')]
    private $hasTrial;

    #[SerializedName('trial_length_days')]
    #[Assert\PositiveOrZero]
    #[Assert\Type('integer')]
    private $trialLengthDays;

    public function getSubscriptionPlan()
    {
        return $this->subscriptionPlan;
    }

    public function setSubscriptionPlan($subscriptionPlan): void
    {
        $this->subscriptionPlan = $subscriptionPlan;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): void
    {
        $this->price = $price;
    }

    public function getPaymentDetails()
    {
        return $this->paymentDetails;
    }

    public function setPaymentDetails($paymentDetails): void
    {
        $this->paymentDetails = $paymentDetails;
    }

    public function getSeatNumber(): int
    {
        return $this->seatNumber;
    }

    public function setSeatNumber(int $seatNumber): void
    {
        $this->seatNumber = $seatNumber;
    }

    public function getHasTrial()
    {
        return $this->hasTrial;
    }

    public function setHasTrial($hasTrial): void
    {
        $this->hasTrial = $hasTrial;
    }

    public function getTrialLengthDays()
    {
        return $this->trialLengthDays;
    }

    public function setTrialLengthDays($trialLengthDays): void
    {
        $this->trialLengthDays = $trialLengthDays;
    }
}
