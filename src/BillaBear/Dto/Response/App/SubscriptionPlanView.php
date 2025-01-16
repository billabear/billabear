<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App;

use BillaBear\Dto\Generic\App\SubscriptionPlan;
use Symfony\Component\Serializer\Annotation\SerializedName;

class SubscriptionPlanView
{
    #[SerializedName('subscription_plan')]
    private SubscriptionPlan $subscriptionPlan;

    public function getSubscriptionPlan(): SubscriptionPlan
    {
        return $this->subscriptionPlan;
    }

    public function setSubscriptionPlan(SubscriptionPlan $subscriptionPlan): void
    {
        $this->subscriptionPlan = $subscriptionPlan;
    }
}
