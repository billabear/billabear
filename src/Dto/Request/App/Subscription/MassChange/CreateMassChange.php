<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\App\Subscription\MassChange;

use App\Validator\Constraints\PriceExists;
use App\Validator\Constraints\SubscriptionPlanExists;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateMassChange
{
    #[Assert\NotBlank]
    #[SerializedName('change_date')]
    #[Assert\DateTime(format: DATE_RFC3339_EXTENDED)]
    private $changeDate;

    #[SubscriptionPlanExists]
    #[SerializedName('target_plan')]
    private $targetPlan;

    #[SubscriptionPlanExists]
    #[SerializedName('new_plan')]
    private $newPlan;

    #[PriceExists]
    #[SerializedName('new_price')]
    private $newPrice;

    public function getChangeDate()
    {
        return $this->changeDate;
    }

    public function setChangeDate($changeDate): void
    {
        $this->changeDate = $changeDate;
    }

    public function getTargetPlan()
    {
        return $this->targetPlan;
    }

    public function setTargetPlan($targetPlan): void
    {
        $this->targetPlan = $targetPlan;
    }

    public function getNewPlan()
    {
        return $this->newPlan;
    }

    public function setNewPlan($newPlan): void
    {
        $this->newPlan = $newPlan;
    }

    public function getNewPrice()
    {
        return $this->newPrice;
    }

    public function setNewPrice($newPrice): void
    {
        $this->newPrice = $newPrice;
    }
}
