<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\App\Checkout;

use App\Validator\Constraints\PriceExists;
use App\Validator\Constraints\SubscriptionPlanExists;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCheckoutSubscription
{
    #[Assert\NotBlank()]
    #[SubscriptionPlanExists]
    private $plan;

    #[Assert\NotBlank]
    #[PriceExists]
    private $price;

    #[SerializedName('seat_number')]
    #[Assert\Type('integer')]
    #[Assert\Positive]
    private $seatNumber;

    public function getPlan()
    {
        return $this->plan;
    }

    public function setPlan($plan): void
    {
        $this->plan = $plan;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): void
    {
        $this->price = $price;
    }

    public function getSeatNumber()
    {
        return $this->seatNumber;
    }

    public function setSeatNumber($seatNumber): void
    {
        $this->seatNumber = $seatNumber;
    }
}
