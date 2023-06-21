<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\App\Subscription;

use App\Validator\Constraints\PriceExists;
use App\Validator\Constraints\PriceIsValidForPlan;
use App\Validator\Constraints\SubscriptionPlanExists;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[PriceIsValidForPlan]
class UpdatePlan
{
    public const NEXT_CYCLE = 'next-cycle';
    public const WHEN_INSTANTLY = 'instantly';
    public const WHEN_DATE = 'specific-date';

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Choice([self::NEXT_CYCLE, self::WHEN_INSTANTLY, self::WHEN_DATE])]
    private $when;

    #[Assert\DateTime(format: DATE_RFC3339_EXTENDED)]
    private $date;

    #[Assert\NotBlank]
    #[PriceExists]
    #[SerializedName('price')]
    private $priceId;

    #[Assert\NotBlank]
    #[SubscriptionPlanExists]
    #[SerializedName('plan')]
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
