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
class CreateSubscription
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[SerializedName('subscription_plan')]
    #[SubscriptionPlanExists]
    private $subscription_plan;

    #[Assert\Type('string')]
    #[PriceExists]
    #[SerializedName('price')]
    private $price;

    #[Assert\Type('string')]
    private $currency;

    #[Assert\Type('string')]
    private $card_token;

    #[Assert\Choice(choices: ['week', 'month', 'year'])]
    #[Assert\Type('string')]
    private $schedule;

    #[PaymentMethodExists]
    #[SerializedName('payment_details')]
    private $payment_details;

    #[Assert\Positive]
    #[Assert\Type('integer')]
    #[SerializedName('seat_number')]
    private $seat_number = 1;

    #[Assert\Type('boolean')]
    private $deny_trial;

    #[Assert\Type('array')]
    private $metadata;

    #[Assert\Type('boolean')]
    #[SerializedName('customer_eligible_for_trial')]
    private $customer_eligible_for_trial;

    public function getSubscriptionPlan()
    {
        return $this->subscription_plan;
    }

    public function setSubscriptionPlan($subscription_plan): void
    {
        $this->subscription_plan = $subscription_plan;
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
        return $this->payment_details;
    }

    public function setPaymentDetails($payment_details): void
    {
        $this->payment_details = $payment_details;
    }

    public function getSeatNumber()
    {
        return $this->seat_number;
    }

    public function setSeatNumber($seat_number): void
    {
        $this->seat_number = $seat_number;
    }

    public function hasPaymentDetails(): bool
    {
        return isset($this->payment_details);
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    public function getSchedule()
    {
        return $this->schedule;
    }

    public function setSchedule($schedule): void
    {
        $this->schedule = $schedule;
    }

    public function getCardToken()
    {
        return $this->card_token;
    }

    public function setCardToken($card_token): void
    {
        $this->card_token = $card_token;
    }

    public function getDenyTrial()
    {
        return $this->deny_trial;
    }

    public function setDenyTrial($deny_trial): void
    {
        $this->deny_trial = $deny_trial;
    }

    public function getMetadata()
    {
        if (!isset($this->metadata)) {
            return [];
        }

        return $this->metadata;
    }

    public function setMetadata($metadata): void
    {
        $this->metadata = $metadata;
    }

    public function getCustomerEligibleForTrial()
    {
        return $this->customer_eligible_for_trial;
    }

    public function setCustomerEligibleForTrial($customer_eligible_for_trial): void
    {
        $this->customer_eligible_for_trial = $customer_eligible_for_trial;
    }
}
