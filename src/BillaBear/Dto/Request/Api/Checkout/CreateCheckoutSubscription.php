<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Api\Checkout;

use BillaBear\Validator\Constraints\PriceExists;
use BillaBear\Validator\Constraints\SubscriptionPlanExists;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCheckoutSubscription
{
    #[Assert\NotBlank]
    #[SubscriptionPlanExists]
    private $plan;

    #[Assert\NotBlank]
    #[PriceExists]
    private $price;

    #[Assert\Positive]
    #[Assert\Type('integer')]
    #[SerializedName('seat_number')]
    private $seat_number;

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
        return $this->seat_number;
    }

    public function setSeatNumber($seat_number): void
    {
        $this->seat_number = $seat_number;
    }
}
