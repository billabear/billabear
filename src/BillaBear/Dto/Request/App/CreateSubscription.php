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

class CreateSubscription
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[SerializedName('subscription_plan')]
    #[SubscriptionPlanExists]
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

    #[Assert\Positive]
    #[Assert\Type('integer')]
    #[SerializedName('seat_number')]
    private $seatNumber = 1;

    #[Assert\Type('boolean')]
    #[SerializedName('has_trial')]
    private $hasTrial;

    #[Assert\PositiveOrZero]
    #[Assert\Type('integer')]
    #[SerializedName('trial_length_days')]
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
