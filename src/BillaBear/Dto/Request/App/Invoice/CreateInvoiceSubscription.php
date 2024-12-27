<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Invoice;

use BillaBear\Validator\Constraints\PriceExists;
use BillaBear\Validator\Constraints\SubscriptionPlanExists;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateInvoiceSubscription
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
