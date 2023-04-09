<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\App;

use App\Validator\Constraints\PaymentDetailsExists;
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

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[PaymentDetailsExists]
    #[SerializedName('payment_details')]
    private $paymentDetails;

    #[Assert\Type('integer')]
    #[Assert\Positive]
    private $seatNumbers = 1;

    #[SerializedName('has_trial')]
    #[Assert\Type('boolean')]
    private $hasTrial;

    #[SerializedName('trial_length_days')]
    #[Assert\Positive]
    #[Assert\Type('integer')]
    private $trialLengthDays;

    /**
     * @return mixed
     */
    public function getSubscriptionPlan()
    {
        return $this->subscriptionPlan;
    }

    /**
     * @param mixed $subscriptionPlan
     */
    public function setSubscriptionPlan($subscriptionPlan): void
    {
        $this->subscriptionPlan = $subscriptionPlan;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getPaymentDetails()
    {
        return $this->paymentDetails;
    }

    /**
     * @param mixed $paymentDetails
     */
    public function setPaymentDetails($paymentDetails): void
    {
        $this->paymentDetails = $paymentDetails;
    }

    public function getSeatNumbers(): int
    {
        return $this->seatNumbers;
    }

    public function setSeatNumbers(int $seatNumbers): void
    {
        $this->seatNumbers = $seatNumbers;
    }

    /**
     * @return mixed
     */
    public function getHasTrial()
    {
        return $this->hasTrial;
    }

    /**
     * @param mixed $hasTrial
     */
    public function setHasTrial($hasTrial): void
    {
        $this->hasTrial = $hasTrial;
    }

    /**
     * @return mixed
     */
    public function getTrialLengthDays()
    {
        return $this->trialLengthDays;
    }

    /**
     * @param mixed $trialLengthDays
     */
    public function setTrialLengthDays($trialLengthDays): void
    {
        $this->trialLengthDays = $trialLengthDays;
    }
}
