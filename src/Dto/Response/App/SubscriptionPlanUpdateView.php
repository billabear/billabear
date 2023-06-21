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

namespace App\Dto\Response\App;

use App\Dto\Generic\App\SubscriptionPlan;
use Symfony\Component\Serializer\Annotation\SerializedName;

class SubscriptionPlanUpdateView
{
    #[SerializedName('subscription_plan')]
    private SubscriptionPlan $subscriptionPlan;

    private array $prices;

    private array $features;

    public function getPrices(): array
    {
        return $this->prices;
    }

    public function setPrices(array $prices): void
    {
        $this->prices = $prices;
    }

    public function getFeatures(): array
    {
        return $this->features;
    }

    public function setFeatures(array $features): void
    {
        $this->features = $features;
    }

    public function getSubscriptionPlan(): SubscriptionPlan
    {
        return $this->subscriptionPlan;
    }

    public function setSubscriptionPlan(SubscriptionPlan $subscriptionPlan): void
    {
        $this->subscriptionPlan = $subscriptionPlan;
    }
}
