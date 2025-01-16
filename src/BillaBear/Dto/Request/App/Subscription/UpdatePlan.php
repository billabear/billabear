<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Subscription;

use BillaBear\Validator\Constraints\PriceExists;
use BillaBear\Validator\Constraints\PriceIsValidForPlan;
use BillaBear\Validator\Constraints\SubscriptionPlanExists;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[PriceIsValidForPlan]
class UpdatePlan
{
    public const NEXT_CYCLE = 'next-cycle';
    public const WHEN_INSTANTLY = 'instantly';
    public const WHEN_DATE = 'specific-date';

    #[Assert\Choice([self::NEXT_CYCLE, self::WHEN_INSTANTLY, self::WHEN_DATE])]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $when;

    #[Assert\DateTime(format: DATE_RFC3339_EXTENDED)]
    private $date;

    #[Assert\NotBlank]
    #[PriceExists]
    #[SerializedName('price')]
    private $priceId;

    #[Assert\NotBlank]
    #[SerializedName('plan')]
    #[SubscriptionPlanExists]
    private $planId;

    public function getPriceId()
    {
        return $this->priceId;
    }

    public function setPriceId($priceId): void
    {
        $this->priceId = $priceId;
    }

    public function getPlanId()
    {
        return $this->planId;
    }

    public function setPlanId($planId): void
    {
        $this->planId = $planId;
    }

    public function getWhen()
    {
        return $this->when;
    }

    public function setWhen($when): void
    {
        $this->when = $when;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date): void
    {
        $this->date = $date;
    }
}
